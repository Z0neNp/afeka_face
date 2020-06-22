# afeka_face

## Environment setup
You should use a Linux OS, WSL-Ubuntu-18.04 is recommended.

**Apache HTTP Server 2.4.43**
- Download the .tar file:
  wget http://us.mirrors.quenda.co/apache//httpd/httpd-2.4.43.tar.gz
- Unpack the .tar file:
  tar -xzf httpd-2.4.43.tar.gz
- Navigate to the srclib directory inside the httpd-2.4.43:
  cd httpd-2.4.43/srclib
*If you already have the APR, skip the following 3 steps.*
- Download the APR library. It provides a commonly agreed upon cross-platform API:
  wget https://apache.mivzakim.net//apr/apr-1.7.0.tar.gz
  wget https://apache.mivzakim.net//apr/apr-util-1.6.1.tar.gz
- Unpack the .tar to the httpd-2.4.43/srclib:
  tar -xzf apr-1.7.0.tar.gz
  tar -xzf apr-util-1.6.1.tar.gz
- Remove the versions from the APR files
  mv apr-1.7.0 apr
  mv apr-util-1.6.1 apr-util
* If you already have the PCRE, skip the following 3 steps.*
- Download the PCRE. It provides regular expression pattern matching using syntax similar to Perl 5.
  cd ~/
  wget https://ftp.pcre.org/pub/pcre/pcre-8.44.tar.gz
- Unpack the .tar file:
  tar -xzf pcre-8.44.tar.gz
- Navigate to the pcre-8.44 directory, configure it's destination and compile it:
  cd pcre-8.44
  ./configure --prefix=/usr/local/pcre
  make
  make install
* If you already have the Expat, skip the following 3 steps.*
- Download the Expat. It provides an XML parser for large files.
  cd ~/
  wget https://github.com/libexpat/libexpat/releases/download/R_2_2_9/expat-2.2.9.tar.gz
- Unpack the .tar file:
  tar -xzf expat-2.2.9.tar.gz
- Navigate to the expat-2.2.9 directory, configure it's destination and compile it:
  cd expat-2.2.9
  ./configure --prefix=/usr/local/expat
  make
  make install
- Navigate to the httpd-2.4.43, build and install Apache
  ./configure --enable-so --with-pcre=/usr/local/pcre --with-expat=/usr/local/expat
  make
  make install
