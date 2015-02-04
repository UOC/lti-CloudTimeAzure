###################
Windoes Azure Client
###################


Creating certificates:

Create Client (*.pem) Certificate

openssl req -x509 -nodes -days 365 -newkey rsa:1024 -keyout mycert.pem -out mycert.pem

Create Server (*.cer) Certificate

openssl x509 -inform pem -in mycert.pem -outform der -out mycert.cer