#!/usr/bin/env python3
"""Build the homepage hero film from the original photoshoot clips.

Takes the 8 clips used in the film, cuts the right 2.6 s from each (the part where the
model's face is in frame), crops them for desktop (wide) and mobile (portrait) around the
face, colour-grades each clip, cross-fades them together and writes the six files the
site uses into assets/videos/.

Needs only Python 3 and ffmpeg (with libx264 and libvpx-vp9, as in the normal Manjaro /
Arch package):  sudo pacman -S --needed python ffmpeg

Usage (from the repository folder):
    python3 tools/make-hero-film.py ~/path/to/original/clips
    python3 tools/make-hero-film.py ~/clips --fast       # quick MP4-only test render
    python3 tools/make-hero-film.py ~/clips --out ~/film # write somewhere else

Clips are found by the number in their file name (e.g. anything containing 1000035254,
such as VID_1000035254.mp4 or 1000035254.MOV), so they can keep their original names.
"""
import argparse
import json
import os
import shutil
import subprocess
import sys
import time

# ---------------------------------------------------------------------------------------
# The shots. Per clip:
#   start  - second in the clip where the shot begins (after the logo intro, face in frame)
#   dy     - top of the desktop crop, as a fraction of the frame height
#   mx     - left edge of the mobile crop, as a fraction of the frame width
#   r/g/b  - white balance gains (removes the lavender/pink or green cast)
#   lo/hi  - black / white levels (lo deepens washed-out blacks)
#   gamma  - midtone brightness (>1 brightens faces)
# Clips 1000054458 and 1000054462 are left out: they are framed from the neck down.
# ---------------------------------------------------------------------------------------
SHOTS = [
    # id           start  dy      mx      r       g       b       lo      hi      gamma
    ('1000035254', 9.2, 0.0,    0.3589, 1.1,    1.0046, 1.0,    0.063,  0.9326, 1.267),
    ('1000035255', 9.2, 0.0,    0.1109, 1.0196, 1.0,    1.1,    0.0699, 0.8447, 1.122),
    ('1000035256', 7.6, 0.0,    0.2929, 1.0,    1.0286, 1.0805, 0.14,   0.994,  1.185),
    ('1000054457', 7.6, 0.0071, 0.2549, 1.0,    1.026,  1.0707, 0.14,   0.9967, 1.188),
    ('1000054459', 4.0, 0.1141, 0.2819, 1.0105, 1.0715, 1.0,    0.14,   0.8178, 0.97),
    ('1000054460', 4.0, 0.0,    0.2189, 1.0,    1.0664, 1.0117, 0.14,   0.8,    0.97),
    ('1000054461', 6.4, 0.0151, 0.1699, 1.0,    1.1,    1.0277, 0.14,   0.9551, 0.97),
    ('1000054463', 6.4, 0.0406, 0.2029, 1.0,    1.1,    1.0667, 0.1342, 0.8819, 0.97),
]
SEG, XF = 2.6, 0.6            # seconds per shot, cross-fade length -> 16.6 s film
SATURATION = 1.12
DENOISE = 'hqdn3d=1:1:3:3'    # light clean-up before sharpening
SHARPEN = 'unsharp=5:5:0.6:3:3:0'

OUTPUTS = [
    # name,               kind,      size,       x264 CRF, VP9 CRF
    ('hero-film-desktop', 'desktop', (1920, 800), 25, 37),
    ('hero-film-mobile',  'mobile',  (864, 1080), 27, 39),
]
VIDEO_EXT = ('.mp4', '.mov', '.m4v', '.mkv', '.webm', '.avi')


def die(msg):
    print('\nError: ' + msg, file=sys.stderr)
    sys.exit(1)


def find_clip(folder, clip_id):
    hits = [f for f in os.listdir(folder)
            if clip_id in f and f.lower().endswith(VIDEO_EXT) and not f.startswith('.')]
    if len(hits) > 1:
        hits.sort(key=lambda f: os.path.getsize(os.path.join(folder, f)), reverse=True)
        print(f'  note: several files match {clip_id}, using the largest: {hits[0]}')
    return os.path.join(folder, hits[0]) if hits else None


def probe(path):
    out = subprocess.run(['ffprobe', '-v', 'error', '-select_streams', 'v:0',
                          '-show_entries', 'stream=width,height:stream_side_data=rotation:format=duration',
                          '-of', 'json', path], capture_output=True, text=True)
    if out.returncode:
        die(f'could not read {path}:\n{out.stderr}')
    d = json.loads(out.stdout)
    s = d['streams'][0]
    w, h = s['width'], s['height']
    rot = 0
    for sd in s.get('side_data_list', []) or []:
        rot = int(sd.get('rotation', 0) or 0)
    if abs(rot) in (90, 270):
        w, h = h, w
    return w, h, float(d['format'].get('duration', 0))


def even(v):
    return int(v) // 2 * 2


def crop_for(kind, shot, w, h):
    _, _, dy, mx = shot[:4]
    if kind == 'desktop':          # wide 2.4:1, keep the top (heads) in frame
        cw = w if w / 2.4 <= h else h * 2.4
        ch = cw / 2.4
        x = (w - cw) / 2
        y = min(max(dy * h, 0), h - ch)
    else:                           # portrait 4:5 around the face
        ch = h if h * 0.8 <= w else w / 0.8
        cw = ch * 0.8
        x = min(max(mx * w, 0), w - cw)
        y = 0
    return f'crop={even(cw)}:{even(ch)}:{even(x)}:{even(y)}'


def grade_for(shot):
    r, g, b, lo, hi, gamma = shot[4:]
    return (f'colorchannelmixer=rr={r}:gg={g}:bb={b},'
            f'colorlevels=rimin={lo}:gimin={lo}:bimin={lo}:rimax={hi}:gimax={hi}:bimax={hi},'
            f'eq=gamma={gamma}:saturation={SATURATION}:contrast=1.03,{DENOISE}')


def run(args, label):
    t = time.time()
    print(f'  {label} ...', end='', flush=True)
    p = subprocess.run(args, capture_output=True, text=True)
    if p.returncode:
        print(' failed')
        die(p.stderr[-3000:])
    print(f' done ({time.time() - t:.0f}s)')


def main():
    here = os.path.dirname(os.path.abspath(__file__))
    ap = argparse.ArgumentParser(description='Build the Hayabella hero film from the original clips.')
    ap.add_argument('clips', help='folder with the original clips')
    ap.add_argument('--out', default=os.path.join(here, '..', 'assets', 'videos'),
                    help='where to write the films (default: the site\'s assets/videos)')
    ap.add_argument('--fast', action='store_true', help='quick MP4-only test render, lower quality')
    a = ap.parse_args()

    for tool in ('ffmpeg', 'ffprobe'):
        if not shutil.which(tool):
            die(f'{tool} not found. Install it with:  sudo pacman -S --needed ffmpeg')
    enc = subprocess.run(['ffmpeg', '-hide_banner', '-encoders'], capture_output=True, text=True).stdout
    for codec in ('libx264', 'libvpx-vp9'):
        if codec not in enc:
            die(f'your ffmpeg has no {codec} encoder.')

    folder = os.path.expanduser(a.clips)
    if not os.path.isdir(folder):
        die(f'folder not found: {folder}')
    out_dir = os.path.abspath(os.path.expanduser(a.out))
    os.makedirs(out_dir, exist_ok=True)

    print(f'Clips from: {folder}')
    shots, missing = [], []
    for shot in SHOTS:
        path = find_clip(folder, shot[0])
        if not path:
            missing.append(shot[0])
            continue
        w, h, dur = probe(path)
        if dur and dur < shot[1] + SEG:
            die(f'{os.path.basename(path)} is only {dur:.1f}s long, but the shot starts at {shot[1]}s.\n'
                f'Use the untrimmed clip (the same one the small copies were made from).')
        print(f'  {shot[0]}: {os.path.basename(path)}  {w}x{h}  {dur:.1f}s')
        shots.append((shot, path, w, h))
    if missing:
        die('no file found for: ' + ', '.join(missing) + f'\nPut all {len(SHOTS)} clips in {folder}.')

    print(f'Writing to: {out_dir}')
    for name, kind, (ow, oh), crf264, crf_vp9 in OUTPUTS:
        args = ['ffmpeg', '-hide_banner', '-y']
        for shot, path, _, _ in shots:
            args += ['-ss', str(shot[1]), '-t', str(SEG), '-i', path]
        parts = []
        for i, (shot, _, w, h) in enumerate(shots):
            parts.append(f'[{i}:v]{grade_for(shot)},{crop_for(kind, shot, w, h)},'
                         f'scale={ow}:{oh}:flags=lanczos,{SHARPEN},setsar=1,fps=30,format=yuv420p,settb=AVTB[v{i}]')
        last = 'v0'
        for i in range(1, len(shots)):
            parts.append(f'[{last}][v{i}]xfade=transition=fade:duration={XF}:offset={i * (SEG - XF):.2f}[x{i}]')
            last = f'x{i}'
        common = ['-filter_complex', ';'.join(parts), '-map', f'[{last}]', '-an', '-pix_fmt', 'yuv420p']
        mp4 = os.path.join(out_dir, name + '.mp4')
        print(f'{name} ({ow}x{oh}):')
        preset = 'veryfast' if a.fast else 'slow'
        run(args + common + ['-c:v', 'libx264', '-profile:v', 'high', '-crf', str(crf264), '-preset', preset,
                             '-movflags', '+faststart', mp4], 'MP4')
        if not a.fast:
            run(args + common + ['-c:v', 'libvpx-vp9', '-b:v', '0', '-crf', str(crf_vp9), '-row-mt', '1',
                                 '-deadline', 'good', '-cpu-used', '3', os.path.join(out_dir, name + '.webm')], 'WebM')
        run(['ffmpeg', '-hide_banner', '-y', '-ss', '0.5', '-i', mp4, '-frames:v', '1', '-q:v', '3',
             os.path.join(out_dir, name + '-poster.jpg')], 'poster')

    print('\nFinished. Files:')
    for name, *_ in OUTPUTS:
        for ext in ('.mp4', '.webm', '-poster.jpg'):
            p = os.path.join(out_dir, name + ext)
            if os.path.exists(p):
                print(f'  {p}  ({os.path.getsize(p) / 1e6:.1f} MB)')
    if a.fast:
        print('(--fast: test render only; run again without --fast for the final files)')


if __name__ == '__main__':
    main()
