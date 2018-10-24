# HBS CTF Writeup

## Playing with JPG ##

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



## Hidden File ##

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



## Useless Extension ##

Hint: Extensions are useless like me

File: hbs.pdf

Diberikan file pdf, namun saat dibuka file tersebut rusak, maka kita gunakan command "file" untuk mengetahui bentuk file tersebut

Command:
```file hbs.pdf```

Output:
```
hbs.pdf: Zip archive data, at least v2.0 to extract
```

Ternyata outputnya adalah sebuah file zip, dan ketika saya extract menghasilkan file "data.txt", dan saya identifikasi file tersebut berisi base64. Lalu saya pun mendecode file tersebut dan meredirectnya ke file "output"

Command:
```cat data.txt | base64 -d > output```

Setelah itu saya identifikasi isi file output tersebut dan ternyata adalah sebuah file gif

Command:
```file output```

Output:
```
output: GIF image data, version 89a, 510 x 100
```

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



## Fix Me ##

Hint: Maybe u shall check my hex :)

File: ezforen.jpg

Kita diberikan sebuah file rusak yang harus diperbaiki. Pertama, saya cek hex dari file tersebut dan disini saya menggunakan ghex untuk melihatnya

Command: ```ghex ezforen.jpg```

Command tersebut akan membuka hex editor, karena bentuknya gui dan saya malas screenshot, saya kasih saja hexnya langsung

Before:
```
53 50 30 31 00 10 4A 46 49 46 00 01 01 01 00 48
```
After:
```
FF D8 FF E0 00 10 4A 46 49 46 00 01 01 01 00 48
```

Setelah itu tinggal save file tersebut dan buka filenya, maka akan muncul flag di file gambar tersebut

Flag: ```HBS{Header_file_can change_everything}```



## Basic Incident Handling ##

Hint: Need help ? here

File: easy.pcapng

Diberikan sebuah pcapng capture file, maka langsung saja dibuka dengan wireshark

Command: ```wireshark easy.pcapng```

Maka akan terbuka wireshark dalam bentuk GUI, lalu klik Analyze > Follow > TCP Stream atau dengan Ctrl + Alt + Shift + C dan akan terbuka window seperti pada ss berikut

![Wireshark](https://github.com/bl33dz/Writeup/raw/master/HBS/wireshark_ss.png)

Dan terlihat ada sesuatu di bagian User-Agent maka langsung saja didecode dan muncul lah flagnya

Flag: ```HBS{s1mple_n3tw0rk_for3ns!c}```



## My Cute Cat ##

Hint: Please help my cat here

Web: http://chall.hungrybirds.org:8030/

Diberikan sebuah web bergambar kucing github, lalu saya menduga terdapat folder .git di web tersebut. Ketika saya cek ternyata benar, namun sayangnya mendapat http code 403, akhirnya saya menggunakan [git dumper](https://github.com/internetwache/GitTools/) untuk mendapatkan folder tersebut

Command: ```./gitdumper.sh http://chall.hungrybirds.org:8030/.git/ HBS```

Maka akan keluar output yang cukup panjang. Setelah itu selesai, folder .git akan tersimpan di ./HBS/.git dan kita pindah ke folder HBS

Setelah pindah ke folder HBS selanjutnya kita recover file pada git tersebut dengan command "git"

Command: ```git checkout .```

Dan file akan muncul kembali namun flag belum muncul. Selanjutnya kita coba melihat log dari git tersebut

Command: ```git log -p```

Maka akan muncul flag dari chall ini

Flag: ```HBS{__poor_cat_with_poor_git__}```



## Recover the Site ##

Hint: Nemes1s just hacked our website, can you please recover the flag inside ? Nemes1s said there was a hint inside zip file which named credential. Please help us here

Web: http://chall.hungrybirds.org:8020/

Dari hint diatas terdapat file /credential.zip di web tersebut. Dan ketika saya mencoba membukanya ternyata file zip tersebut memang ada. Lalu, saya mengextract file tersebut dan munculah file user.txt dan pass.txt yang keduanya berisi wordlist

Setelah itu saya mencoba melihat source code dari web tersebut dan saya menemukan admin login yang berada di /4dmIn-pAn3L

Saat saya membuka page tersebut, saya dimintai user dan password dengan http authentication. Sayapun langsung mencoba bruteforce login tersebut dengan hydra dan wordlist dari credential diatas

Command: ```hydra -L user.txt -P pass.txt -s 8020 -f chall.hungrybirds.org http-get /4dmIn-pAn3L```

Output:
```
........
[8020][http-get] host: chall.hungrybirds.org   login: Administrator   password: hunter
........
```

Akhirnya saya menemukan user dan passwordnya, dan saya langsung login untuk mendapatkan flagnya

Flag: ```HBS{12345_say_no_to_defacing_12345}```

## Hello, Old Friend ##

Hint: Hemmm, mungkin file ini bisa membantu masalah kalian :)

File: encrypt_1.jpg.jpg.jpg.jpg

Diberikan file dengan ekstensi jpg yang diulang ulang, namun ketika dicek ternyata hanya file txt dan berikut isinya

Command: ```cat encrypt_1.jpg.jpg.jpg.jpg```

Output:
```
Aku adalah cipher lama dengan nama Vigenere............
jika kalian butuh password mungkin sebuah logo pada scoreboard bisa membantu kalian ^^

Flag : OVF{b1xlfzv_z1lo_BhtxpzJzuvz_e3l}
```

Nah terlihat nama chipernya adalah Vigenere, dan passwordnya adalah "HungryBirds" seperti pada logo. Langsung saya decrypt dengan [Enigmator](http://merricx.github.io/enigmator/) dan pilih Vigenere dan akan muncul output berupa flag

Flag: ```HBS{v1gnere_w1th_HungryBirds_k3y}```

## Stop Spam Me !!! ##

Hint: WTF THIS SHIT SPAM MESSAGE !!!!

File: encrypt_2.rar.zip.jfif.7z

Diberikan lagi sebuah file namun ternyata file tersebut hanyalah ASCII text, berikut potongan isinya

Command: ```cat encrypt_2.rar.zip.jfif.7z```

Output:
```
Dear Friend , Especially for you - this breath-taking 
info . If you are not interested in our publications 
and wish to be removed from our lists, simply do NOT 
respond and ignore this mail . This mail is being sent 
in compliance with Senate bill 1623 , Title 3 ; Section 
301 . THIS IS NOT A GET RICH SCHEME . Why work for 
somebody else when you can become rich within 45 weeks 
. Have you ever noticed society seems to be moving 
faster and faster and more people than ever are surfing 
the web . Well, now is your chance to capitalize on
.......
.......
```
Dari potongan pesan tersebut saya menduga teks tersebut merupakan spam encode, lalu saya mencoba mendecode di [Spammimic](http://www.spammimic.com/decode.shtml) dan akan menghasilkan output berupa flag

Flag: ```HBS{0h_wh4t_1s_th1s_sh1t_crypt0}```

## Scanme ##

Hint: scanme bruh... *(space) replace with (_)

File: qrcode_1.png

Pada chall ini diberikan sebuah qr code, sayapun mendecodenya dengan zbarimg

Command: ```zbarimg qrcode_1.png```

Output:
```
QR-Code:ymwuz tmdu ymwuz egemt emvm yqzvmpu ymzgeum kmzs ymzgeum
scanned 1 barcode symbols from 1 images in 0.03 seconds
```

Dari output diatas saya menduga bahwa itu adalah sebuah chiper yaitu Caesar Chiper, lalu saya mencoba bruteforce shiftnya dengan [Enigmator](http://merricx.github.io/enigmator/cipher/caesar_shift.html) dan akan muncul sebuah teks yaitu

Output: ```makin hari makin susah saja menjadi manusia yang manusia```

Lalu dari hint yang terdapat soal, kita disuruh merubah spasi menjadi underscore

Command: ```echo makin hari makin susah saja menjadi manusia yang manusia | tr ' ' '_'```

Output: ```makin_hari_makin_susah_saja_menjadi_manusia_yang_manusia```

Dan masukkan ke dalam format HBS{flag}

Flag: HBS{makin_hari_makin_susah_saja_menjadi_manusia_yang_manusia}

## Dibalik Pertahanan Sixty Four ##

Hint: my friend say this is base64 encryption, but when i tried to decode, the return always weird character

Code: ++QYndmbhRXZ0Bibhdmbv12bgkGZhpGIzV2crV3cgEmbphWakBCbpNXYoJXZiBSb1xWZiBCahNXdzBCahxGdhdmbhNHIhlXYrBibpdmbpBCahxWYzBSZyV2agkGZhpGIolGbp1WZtBCLzl2Zvx2blRWagsWY0BSahxWaulGZggWYn5WZ0BycpNXYmBSahRXYrlGZgMXYyV2agMXasFGdpBXYrBCchNWakBibh5WYrBycp5Wdt92agEmcptWakBSayl2a

Diberikan sebuah string yang seperti base64 tapi sepertinya base64 tersebut dibalik, dan karena biasanya base64 dibelakangnya adalah double '=' maka saya merubah '+' dengan '='

Command: ```echo "++QYndmbhRXZ0Bibhdmbv12bgkGZhpGIzV2crV3cgEmbphWakBCbpNXYoJXZiBSb1xWZiBCahNXdzBCahxGdhdmbhNHIhlXYrBibpdmbpBCahxWYzBSZyV2agkGZhpGIolGbp1WZtBCLzl2Zvx2blRWagsWY0BSahxWaulGZggWYn5WZ0BycpNXYmBSahRXYrlGZgMXYyV2agMXasFGdpBXYrBCchNWakBibh5WYrBycp5Wdt92agEmcptWakBSayl2a" | rev | tr '+' '==' | base64 -d```

Output: ```kiri dikira komunis kanan dicap kapitalis keras dikatai fasis tengah dinilai tak ideologis, memilih jadi kere salah ingin kaya sangatlah susah belum berhasil dihina sukses jadi omongan tetangga```

Karena tidak ada kemungkinan lain saya mencoba memasukkan output tersebut kedalam format flag dan ternyata benar

Flag: HBS{kiri dikira komunis kanan dicap kapitalis keras dikatai fasis tengah dinilai tak ideologis, memilih jadi kere salah ingin kaya sangatlah susah belum berhasil dihina sukses jadi omongan tetangga}

## My server has been hacked ##

Hint: Serverku diretas. Ku mohon bantu aku temukan jejaknya

File: hacker.pcapng

Diberikan sebuah capture file, maka saya pun mencoba membukanya dengan wireshark

Ketika saya buka saya dengan wireshark sayapun langsung mencoba melihat TCP Streamnya dan saya temukan sebuah clue yaitu "flag? langkahi dulu ftpku"

Karena setelah saya cari cari dengan wireshark namun tidak ketemu saya mencoba memisahkan file tersebut dengan foremost

Command: ```foremost hacker.pcapng```

Setelah itu saya mencoba melihat apa saja yang ada dalam file hacker.pcapng dan terdapat sebuah file zip dan png

Saya mencoba unzip file zip tersebut dan tidak ada flag disana, yang ada hanyalah sebuah teks "Where is the flag?"

Lalu saya tertarik dengan file pngnya dan ketika saya buka ternyata sebuah qr code, maka saya scan qr tersebut dan munculah flag

Command: ```zbarimg 00000148.png```

Flag: ```HBS{forensic_is_ezpz}```

## Hacked by Medusa ##

Hint: Kali ini situsku diretas oleh hacker dengan nickname Medusa dan sepertinya dia meninggalkan sesuatu. Dapatkah kamu menemukannya?

File: access.log

Diberikan file access log, setelah saya buka saya melihat pada file tersebut terdapat sebuah pola yaitu directory berbentuk seperti kode biner

Maka saya melakukan scripting untuk mengambil code biner pada file tersebut

Code terdapat pada solved.php di directory ini

Karena saya malas melakukan scripting untuk convert biner ke ascii maka saya gunakan online tools saja (bisa dicari di google)

Flag: ```HBS{hacked_by_medusa}```


