
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

#NOTE about -UseBasicParsing cmdlet in powshell
#https://stackoverflow.com/questions/38005341/the-response-content-cannot-be-parsed-because-the-internet-explorer-engine-is-no
#For future visitors interested in what UseBasicParsing is doing: Uses the response object for HTML content without Document Object Model (DOM) parsing.  This parameter is required when Internet Explorer is not installed on the computers, such as on a Server Core installation of a Windows Server operating system. copied from here: 
#
#https://docs.microsoft.com/de-de/powershell/module/Microsoft.PowerShell.Utility/Invoke-WebRequest?view=powershell-7.2&viewFallbackFrom=powershell-3.0
#-UseBasicParsing
#
#This parameter has been deprecated. Beginning with PowerShell 6.0.0, all Web requests use basic parsing only. This parameter is included for backwards compatibility only and any use of it has no effect on the operation of the cmdlet.
#Type:	SwitchParameter
#Position:	Named
#Default value:	None
#Accept pipeline input:	False
#Accept wildcard characters:	False


# Invoke-RestMethod -Method Get -Uri "http://ip-api.com/json/$external_ip"
# NOTE about install 
# psexec \\COMPUTER_NAME -s \\Server\NetLogon\OCS-NG-Windows-Agent-Setup.exe /S /NOSPLASH /SERVER=http://my_ocs_server/ocsinventory



#NOTE about html and url encode decode 

#https://global-sharepoint.com/powershell/how-to-encode-and-decode-a-url-using-powershell/
#Add-Type -AssemblyName System.Web
#
#$webURL="https://globalsharepoint.sharepoint.com/sites/TestSite/Shared Documents/"
#Write-Host "The original url is " $webURL -ForegroundColor Green
##The below code is used to encode the URL
#$urlToEncode = $webURL
#$encodedURL = [System.Web.HttpUtility]::UrlEncode($urlToEncode) 
#Write-Host "The encoded url is: " $encodedURL -ForegroundColor Green
##Encode URL code ends here
#
##The below code is used to decode the URL.
#$urlTodDecode = $encodedURL
#$decodedURL = [System.Web.HttpUtility]::UrlDecode($urlTodDecode)
#Write-Host "The decoded url is: " $decodedURL -ForegroundColor Green
##Decode URL code ends here.


#https://stackoverflow.com/questions/10082217/what-is-the-best-way-to-escape-html-specific-characters-in-a-string-powershell
#There's a class that will do this in System.Web.
#
#Add-Type -AssemblyName System.Web
#[System.Web.HttpUtility]::HtmlEncode('something <somthing else>')
#
#You can even go the other way:
#
#[System.Web.HttpUtility]::HtmlDecode('something &lt;something else&gt;')


#requires -Version 3

Add-Type -AssemblyName System.Web

$IPAddress = (Invoke-WebRequest -uri "icanhazip.com" -UseBasicParsing).Content

$tunnel = Get-WmiObject -Class Win32_NetworkAdapterConfiguration -Filter 'IPEnabled = True'

$request = Invoke-RestMethod -Method Get -Uri "http://ip-api.com/json/$IPAddress"



$osmap = 'https://www.openstreetmap.org/?mlat='+$request.lat+'&mlon='+$request.lon
$google = 'https://www.google.com/maps/search/?api=1&query='+ $request.lat + ',' + $request.lon


<a href="yourpage.htm" target="_blank" onClick="window.open('yourpage.htm','pagename','resizable,height=600,width=800'); return false;">New Page</a>
#$linkOmap = [System.Web.HttpUtility]::HtmlEncode( '<a href="'+ [System.Web.HttpUtility]::UrlEncode($osmap)  +'">OpenStreetMap</a>' )
#$linkGoogle = [System.Web.HttpUtility]::HtmlEncode( '<a href="'+ [system.Web.HttpUtility]::UrlEncode($google) +'">GMap</a>' )

$linkOmap = [System.Web.HttpUtility]::HtmlEncode( '<a href="'+ $osmap +'" target="_blank" onClick="window.open('+ $osmap +',"pagename","resizable,height=800,width=600");return false">OpenStreetMap</a>' )
$linkGoogle = [System.Web.HttpUtility]::HtmlEncode( '<a href="'+ $google +'" target="_blank" onClick="window.open('+$google+',"pagename","resizable,height=800,width=600")">GoogleMap</a>' )



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
    $xml += "<OSMAP>" + $linkOmap + "</OSMAP>`n"
    $xml += "<GOOGLE>" + $linkGoogle + "</GOOGLE>`n"
    $xml += "</GEOLOCATION>`n"



# Force UTF8 enconding
[Console]::OutputEncoding = [System.Text.Encoding]::UTF8
[Console]::WriteLine($xml)























