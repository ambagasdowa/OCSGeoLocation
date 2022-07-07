###############################################################################
## OCSINVENTORY-NG
## Copyleft Léa DROGUET 2020
## Web : http://www.ocsinventory-ng.org
##
## This code is open source and may be copied and modified as long as the source
## code is always made freely available.
## Please refer to the General Public Licence http://www.gnu.org/ or Licence.txt
################################################################################

package Apache::Ocsinventory::Plugins::Geolocation::Map;
 
use strict;
 
use Apache::Ocsinventory::Map;
$DATA_MAP{geolocation} = {
   mask => 0,
   multi => 1,
   auto => 1,
   delOnReplace => 1,
   sortBy => 'ID',
   writeDiff => 0,
   cache => 0,
   fields => {

               'CLIENT' => {},
               'IP' => {},
               'PHONE' => {},
               'IMEI' => {},
               'COUNTRY' => {},
               'COUNTRYCODE' => {},
               'REGION' => {},
               'REGIONNAME' => {},
               'CITY' => {},
               'ZIP' => {},
               'LATITUDE' => {},
               'LONGITUDE' => {},
               'TIMEZONE' => {},
               'ISP' => {},
               'ORG' => {},
               'ASLABEL' => {},
               'OSMAP' => {},
               'GOOGLE' => {},
               'BING' => {},
               'OSMOSE' => {},
               'HERE' => {},
               'SERVER_ONE' => {},
               'SERVER_TWO' => {},
               'TIMEHOST' => {}

   }
};
1;
