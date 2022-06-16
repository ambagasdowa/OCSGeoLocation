
# NOTE retrieve geolocation for client in ocs inventory
# servers :
# Invoke-WebRequest -uri "url" 
#
# https://api.ipify.org/
# https://ipinfo.io/json
# http://ifconfig.me/ip
# http://icanhazip.com
# http://ident.me
# http://smart-ip.net/myip

# Invoke-RestMethod -Method Get -Uri "http://ip-api.com/json/$external_ip"


#requires -Version 3


$IPAddress = (Invoke-WebRequest -uri "icanhazip.com").Content

$tunnel = Get-WmiObject -Class Win32_NetworkAdapterConfiguration -Filter 'IPEnabled = True'

$request = Invoke-RestMethod -Method Get -Uri "http://ip-api.com/json/$IPAddress"


    $xml += "<GEOLOCATION>`n"
    $xml += "<IP>" + $request.query + "</IP>`n"
    $xml += "<COUNTRY>" + $request.country + "</COUNTRY>`n"
    $xml += "<COUNTRYCODE>" + $request.countryCode + "</COUNTRYCODE>`n"
    $xml += "<REGION>" + $request.region + "</REGION>`n"
    $xml += "<REGIONNAME>" + $request.regionName + "</REGIONNAME>`n"
    $xml += "<CITY>" + $request.city + "</CITY>`n"
    $xml += "<ZIP>" + $request.zip + "</ZIP>`n"
    $xml += "<LATITUDE>" + $request.lat + "</LATITUDE>`n"
    $xml += "<LONGITUDE>" + $request.lon + "</LONGITUDE>`n"
    $xml += "<TIMEZONE>" + $request.timezone + "</TIMEZONE>`n"
    $xml += "<ISP>" + $request.isp + "</ISP>`n"
    $xml += "<ORG>" + $request.org + "</ORG>`n"
    $xml += "<ASLABEL>" + $request.as + "</ASLABEL>`n"
    $xml += "<OSMAP>" + '<a href="https://www.openstreetmap.org/#map=13/'+$request.lat+'/'+$request.lon+'">map</a>' + "</OSMAP>`n"
    $xml += "<GOOGLE>" + 'https://www.google.com/maps/@'+ $request.lat + ',' + $request.lon +',15z' + "</GOOGLE>`n"
    $xml += "</GEOLOCATION>`n"



# Force UTF8 enconding
[Console]::OutputEncoding = [System.Text.Encoding]::UTF8
[Console]::WriteLine($xml)























