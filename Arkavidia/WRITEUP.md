## Arkavidia 5.0 CTF Writeup

[![HungryBirds](https://img.shields.io/badge/team-HungryBirds-red.svg)](http://hungrybirds.org/)
[![Bleedz](https://img.shields.io/badge/writer-Bleedz-blue.svg)](https://github.com/bl33dz/)

Arkavidia 5.0 CTF adalah kompetisi yang diadakan oleh Institut Teknologi Bandung yang diikuti oleh mahasiswa atau siswa SMA/SMK sederajat.

## Table of Contents

* Forensic
  * [YaQueen](#yaqueen)
  * [Magic](#magic)
  * [ranger](#ranger)
* Web
  * [Optimus Prime](#optimus-prime)
  * [Kulit Manggis](#kulit-manggis)
  * [Fancafe](#fancafe)
* Misc
  * [Welcome](#welcome)
  * [geet](#geet)

## YaQueen

Type: Forensic  
File: YaQueen.jpg ([Here](YaQueen.jpg))


Diberikan sebuah file bernama "YaQueen.jpg" dan ketika kami buka muncul sebuah gambar dari paslon fiktif Nurhadi-Aldo. Lalu kami melakukan cek terhadap file tersebut menggunakan binwalk

```
bleedz@nightmare:~/Writeup/Arkavidia$ binwalk YaQueen.jpg

DECIMAL       HEXADECIMAL     DESCRIPTION
--------------------------------------------------------------------------------
0             0x0             JPEG image data, JFIF standard 1.01
118594        0x1CF42         Zip archive data, at least v2.0 to extract, name: data/
118629        0x1CF65         Zip archive data, at least v2.0 to extract, compressed size: 446, uncompressed size: 631 compressed size: 446, uncompressed size: 631, name: data/um_100.jpg
120099        0x1D523         Zip archive data, at least v2.0 to extract, compressed size: 446, uncompressed size: 631, name: data/um_101.jpg
120590        0x1D70E         Zip archive data, at least v2.0 to extract, compressed size: 446, uncompressed size: 631, name: data/um_102.jpg
..........
```

Setelah melihat output diatas kami mencoba meng-extract file tersebut

```bleedz@nightmare:~/Writeup/Arkavidia$ binwalk -e YaQueen.jpg```

Ketika kami cek hasil dari extract tadi, kami menemukan file jpg yang sangat banyak dan isinya hanyalah gambar kotak kotak hitam putih yang terpisah pisah yang berjumlah 625 gambar. Kami pun menduga bahwa itu adalah kode qr berukuran 25x25 yang dipisah pisah dan kami melakukan scripting untuk menggabungkannya

```python
from PIL import Image
import requests
import re

im = Image.new("RGB", (25,25), 'white')
px = im.load()
y = 0
x = 0
i = 1

while y < 25:
    for x in range(0,25):
        fn = "um_" + str(i) + ".jpg"
        jm = Image.open(fn)
        qx = jm.load()
        px[x,y] = qx[1,1]
        i += 1
        if x == 24:
            y += 1

im.save('qr.png')

url = 'https://zxing.org/w/decode'
files = {'f': open('qr.png', 'rb')}
req = requests.post(url, files=files)
flag = re.search(r'<pre>(.*?)<\/pre>', req.content).group(1)
print "Flag: " + flag
```

Lalu kami hanya tinggal perlu menjalankan script tersebut

```
bleedz@nightmare:~/Writeup/Arkavidia/_YaQueen.jpg.extracted/data$ python solver.py
Flag: Arkav5{McQueenYaQueeeen__}
```

Flag: ```Arkav5{McQueenYaQueeeen__}```

## Magic

Type: Forensic  
File: megic.png ([Here](megic.png))

Diberikan sebuah file bernama "megic.png" dan ketika kami buka ternyata gambarnya rusak. Kamipun meng-cek gambar tersebut dan ternyata isinya hanyalah sebuah data tidak jelas. Kami menduga file tersebut telah dienkripsi dengan xor dan dari analisis(coba coba) kami, panjang key dari xor tersebut adalah 4 bytes dan kami melakukan xor antara 4 bytes pertama file magic dan 4 bytes pertama dari file png asli untuk mendapatkan key-nya

```
>>> xor = [0xe8, 0x22, 0x38, 0x3e]
>>> png = [0x89, 0x50, 0x4e, 0x47]
>>> key = ''.join(chr(x^y) for x,y in zip(png,xor)
>>> print key
arvy
```

Lalu kami melakukan scripting untuk mengembalikan file gambar tersebut kedalam bentuk aslinya

```python
img = open("megic.png").read()
key = "arvy"
res = ""
for i in range(len(img)):
    res += chr(ord(img[i]) ^ ord(key[i % len(key)]))

f = open("result.png", "w")
f.write(res)
f.close()
```

Kami hanya perlu mengeksekusi script tersebut dan akan muncul file "result.png". Ketika kami buka file tersebut maka akan muncul flagnya

![result.png](https://i.ibb.co/NjSNk0m/Screenshot-from-2019-01-13-23-19-48.png)

Flag: ```Arkav5{M4giC_Byte}```

## ranger

Type: Forensic  
File: ranger.pcapng ([Here](ranger.pcapng))

Diberikan sebuah file bernama "ranger.pcapng" dan ketika kami mencoba melihat tcp streamnya, kami mendapati request yang mendapat response base64 dengan range tertentu

Pertama tama kami mencoba meng-extract semua tcp stream-nya ke dalam sebuah folder dengan melakukan scripting menggunakan bash

```bash
folder=ranger
infile=ranger.pcapng
outfile=ranger_tcp
if [ ! -d "$folder" ]; then
    mkdir "$folder"
fi
for stream in $(tshark -nlr $infile -Y tcp.flags.syn==1 -T fields -e tcp.stream | sort -n | uniq | sed 's/\r//'); do
    echo "Processing stream $stream: ${folder}/${outfile}_${stream}.txt"
    tshark -nlr $infile -qz "follow,tcp,raw,$stream" | tail -n +7 | sed 's/^\s\+//g' | xxd -r -p > ranger/${outfile}_${stream}.txt
done
```

Ketika script tersebut dieksekusi maka akan muncul sebuah output seperti berikut jika berhasil

```
bleedz@nightmare:~/Writeup/Arkavidia$ bash ranger_extract.sh
Processing stream 0: ranger/ranger_tcp_0.txt
Processing stream 1: ranger/ranger_tcp_1.txt
Processing stream 2: ranger/ranger_tcp_2.txt
Processing stream 3: ranger/ranger_tcp_3.txt
Processing stream 4: ranger/ranger_tcp_4.txt
Processing stream 5: ranger/ranger_tcp_5.txt
Processing stream 6: ranger/ranger_tcp_6.txt
Processing stream 7: ranger/ranger_tcp_7.txt
Processing stream 8: ranger/ranger_tcp_8.txt
.................
```

Setelah dieksekusi maka akan muncul folder bernama "ranger" berisi tcp stream yang telah diubah menjadi format txt

Kami menduga file tersebut adalah file zip yang diencode base64 dan berjumlah 5 file zip, maka kami melakukan scripting untuk mendapatkan flag

```python
from base64 import b64decode as dec
from zipfile import ZipFile
import re

files = ["gafl.zip", "glaf.zip", "lagf.zip", "galf.zip", "lafg.zip"]
prefix = "/home/bleedz/Writeup/Arkavidia/ranger/"
data = []


for n in files:
    tmpdata = []
    for i in range(40):
        tcp = open(prefix + "ranger_tcp_"+str(i)+".txt", "r")
        line = tcp.readlines()
        dat = []
        if n in line[0]:
            if "Range" in line[2]:
                range = re.search(r'bytes=(.*?)-', line[2].strip(), flags=re.IGNORECASE).group(1)
                dat.append(int(range))
                dat.append(line[-1].strip())
                del range
                tmpdata.append(dat)
        tmpdata = filter(None, tmpdata)
        tmpdata
    data.append(tmpdata)

for d in data:
    d.sort()
    g = []
    for da in d:
        g.append(da[1])
    with open("tmp.zip", "w") as m:
        m.write("".join([dec(h) for h in g]))
    zf = ZipFile("tmp.zip")
    for zx in zf.infolist():
        lx = zf.open(zx)
        lc = lx.readline()
        if "Arkav5{" in lc:
            flag = re.search(r'Arkav5{(.*?)}', lc)
            print "Flag: " + flag.group()
```

Dan ketika dieksekusi maka kita akan mendapat flag dari chall infile

```
bleedz@nightmare:~/Writeup/Arkavidia$ python solver.py
Flag: Arkav5{Mult1_rang3_d0wnl0ad}
```

Flag: ```Arkav5{Mult1_rang3_d0wnl0ad}```

## Optimus Prime

Type: Web  
URL: http://18.222.179.254:10012/ (Link telah mati)

Diberikan sebuah website beralamat http://18.222.179.254:10012/, namun ketika kami cek web tersebut terlihat sangat statis

Maka kami mencoba meng-cek "robots.txt" web tersebut

```
bleedz@nightmare:~$ curl http://18.222.179.254:10012/robots.txt
User-agent: *
Disallow: /mysecret.php
```

Ketika kami membuka http://18.222.179.254:10012/mysecret.php kami mendapat sebuah clue yaitu "head"

![MySecret](https://i.ibb.co/Zfw8ZBq/secret.png)

Kami pun langsung mencoba melihat header dari page tersebut

```
bleedz@nightmare:~$ curl http://18.222.179.254:10012/mysecret.php -Is | grep "Arkav5"
flag: Arkav5{freedom_is_the_right_of_all_sentient_beings__}
```

Flag: ```Arkav5{freedom_is_the_right_of_all_sentient_beings__}```

## Kulit Manggis

Type: Web  
URL: http://18.222.179.254:10013/ (Link telah mati)

Diberikan sebuah web beralamat http://18.222.179.254:10013/ ketika kami cek terdapat comment "?debug=em". Maka ketika kami tambah param tersebut akan muncul source codenya

![Source](https://i.ibb.co/KxcXxTR/source.png)

Terlihat source code yang menginjinkan kita melihat source "test.php" dengan parameter "?superdebug"

![Source2](https://i.ibb.co/gmpCn4d/source2.png)

Dari source tersebut kami menyimpulkan script tersebut memiliki bug dengan fungsi ```extract($_POST)```

![Source3](https://i.ibb.co/m5XM6WC/source3.png)

Kami lalu meng-exploitasi bug tersebut untuk mengubah variable ```$correct```

```
bleedz@nightmare:~$ curl -s -X POST --data "correct=1337" http://18.222.179.254:10013/test.php | grep "Arkav5"
Flag: <code>Arkav5{alw4ys_know_h0w_th3_http_w0rks}</code>
```

Flag: ```Arkav5{alw4ys_know_h0w_th3_http_w0rks}```

## Fancafe

Type: Web  
URL: http://18.223.125.143:10011/ (Link telah mati)  
Source: fancafe.zip ([Here](fancafe.zip))

Tanpa membaca source code nya, kami mencoba melakukan SQL Injection pada
column search. Dengan menggunakan payload ‘or’ dan ‘and’. Dari situ kami mengetahui jika teradapat SQL Injection pada search tersebut.

Ketika menggunakan payload : ‘ saja, web tersebut akan melakukan return Internal
Server Error.

Kami menduga adanya waf pada website tersebut, ketika kami menggunakan " " (space) dan “-- -” , akan tetap melakukan return Internal Server Error, sehingga kami melakukan bypass space menggunakan ```/**/``` dan menggunakan ```#``` untuk melakukan commenting.

Payload: ```HungryBirds‘/**/or/**/1=1#```

![SQLi](https://i.ibb.co/DRjW9y6/sqli.png)

Flag: ```Arkav5{SQLi_adalah_jalan_ninjaku}```

## Welcome

Type: Misc

Seperti yang sudah dikatakan pada deskripsi challenge flagnya terdapat di Slack #misc, jadi kami membuka https://ctfarkavidia.slack.com/messages/CF8UYUL20/

![Welcome](https://i.ibb.co/xqW8HRY/flag.png)

Flag: ```Arkav5{welcome_to_arkav5}```

## geet

Type: Misc  
File: geet.zip ([Here](geet.zip))

Diberikan sebuah file zip yang isinya adalah folder git, lalu kami mencoba apakah terdapat flag dalam log git tersebut

```
bleedz@nightmare:~/Writeup/Arkavidia/geet$ git log -p | grep "Arkav5{"
-Arkav5{git_s4ve_y0uR_h1st0ri3s}
+Arkav5{git_s4ve_y0uR_h1st0ri3s}
```

Flag: ```Arkav5{git_s4ve_y0uR_h1st0ri3s}```

## Thanks to
* Riordan Ganezo
* Rizka Aditya
