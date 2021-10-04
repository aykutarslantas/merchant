# PHALCON 4 - REDIS - MICRO API

Phalcon 4 & Php 7.4 kullanarak basit bir rest-api geliştirdim.
Şunları içerir:

- Sisteme Login olma ve jwt alma
- Yeni sipariş oluşturma
- Sipariş Güncelleme
- Sipariş detayını görme
- Tüm siparişleri görme
- Oturum bilgilerini redis üzerinde tutma

## POSTMAN DOSYASI

Postman dosyasını içe aktararak hızlıca test işlemini gerçekleştirebilirsiniz.

## GELİŞTİRİLEBİLİR

Hızlıca kodlayabilmek için kullanıcı şifresini herhangi bir hash kullanmadan sakladım ayrıca redis'te jwt'yi id bazlı tuttum ilgili id için daha önce token üretilmişse kullanabiliyor.

## DOCKER

Kütüphane eklediğim için dockerfile oluşturamadım kafam karıştı.😥  Yardım etmek isterseniz ilk olarak buradan başlayabilirsiniz 🥳


## KÜTÜPHANELER - PHP

PHP 7.4 TS VC 15

- extension=psr
- extension=phalcon
- extension=redis
