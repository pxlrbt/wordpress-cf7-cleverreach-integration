zip:
	rm -f cf7-cleverreach-integration.zip
	"C:/Program Files/Easy 7-Zip/7z.exe" a -r -tzip -y -xr@.zipignore ../cf7-cleverreach-integration.zip ./*

build: zip