#!/usr/bin/env bash
for file in assets/images/*
do
cwebp -q 80 "$file" -o "$file.webp"
done
