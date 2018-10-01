# HBS CTF Writeup

**Playing with JPG**

Hint: Aku terdapat di bagian gambar itu dan temukan aku
File: yumeno.jpg

Setelah kita download, file tersebut memang terlihat file gambar biasa, namun coba kita cek menggunakan command "file"

Command:
```file yumeno.jpg```

Output:
```
yumeno.jpg: JPEG image data, Exif standard: [TIFF image data, big-endian, direntries=7, orientation=upper-left, xresolution=98, yresolution=106, resolutionunit=2, software=Adobe Photoshop CS4 Macintosh, datetime=2014:01:07 17:35:42], comment: "HBS{this_is_very_e45y}", baseline, precision 8, 700x856, frames 3
```

Dapat dilihat dari output diatas kita dapat menemukan flag di bagian comment

Flag: ```HBS{this_is_very_e45y}```



**Hidden File**

Hint: I'm not a JPG file
File: yumeno2.jpg

Kita diberi gambar sama seperti chall **Playing with JPG** tapi ternyata berbeda, karena dari judulnya adalah **Hidden File** maka coba kita cek menggunakan command "binwalk"

Command:
```binwalk yumeno2.jpg```

Output:
```
DECIMAL       HEXADECIMAL     DESCRIPTION
--------------------------------------------------------------------------------
0             0x0             JPEG image data, JFIF standard 1.01
337247        0x5255F         Zip archive data, at least v1.0 to extract, compressed size: 17, uncompressed size: 17, name: flag.txt
337408        0x52600         End of Zip archive
```

Dari output diatas kita simpulkan file tersebut memiliki file lain dan berbentuk file zip, maka kita unzip saja dengan command "unzip"

Command:
```unzip yumeno2.jpg```

Output:
```
Archive:  yumeno2.jpg
warning [yumeno2.jpg]:  337247 extra bytes at beginning or within zipfile
 (attempting to process anyway)
extracting: flag.txt
```

Nah setelah itu kita buka file "flag.txt" dan kita dapatkan flagnya

Flag: ```HBS{basic_sten0}```



**Useless Extension**

Hint: Extensions are useless like me
File: hbs.pdf

Diberikan file pdf, namun saat dibuka file tersebut rusak, maka kita gunakan command "file" untuk mengetahui bentuk file tersebut

Command:
```file hbs.pdf```

Output:
```hbs.pdf: Zip archive data, at least v2.0 to extract```

Ternyata outputnya adalah sebuah file zip, dan ketika saya extract menghasilkan file "data.txt", dan saya identifikasi file tersebut berisi base64. Lalu saya pun mendecode file tersebut dan meredirectnya ke file "output"

Command:
```cat data.txt | base64 -d > output```

Setelah itu saya identifikasi isi file output tersebut dan ternyata adalah sebuah file gif

Command:
```file output```

Output:
```output: GIF image data, version 89a, 510 x 100```

Dan ketika saya membuka file tersebut dengan imagemagick, file tersebut menampilkan barcode. Lalu saya mendecode barcode tersebut dan menghasilkan output base64 lagi

Command:
```zbarimg output```

Output:
```
CODE-128:SEJTe2Zha2VfM3hfN30=
scanned 1 barcode symbols from 1 images in 0.02 seconds
```

Dan setelah saya decode base64 tersebut muncul lah flagnya

Flag: ```HBS{fake_3x_7}```
