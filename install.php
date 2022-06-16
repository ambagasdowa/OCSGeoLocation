<?php
/**
 * The following functions are used by the extension engine to generate a new table
 * for the plugin / destroy it on removal.
 */


/**
 * This function is called on installation and is used to 
 * create database schema for the plugin
 */

// NOTE 
// http://mysql.rjweb.org/doc.php/latlng 
// https://mariadb.com/kb/en/latitudelongitude-indexing/
// this is insteresting above the data type of geospatial coordinates 
// And we really need to use MySQL's spatial extensions with GIS.?
// in this case i think the answer is not 
// otherwise they exists a postgis extension for postgresql 


#Datatype           Bytes       resolution
#   ------------------ -----  --------------------------------
#   Deg*100 (SMALLINT)     4  1570 m    1.0 mi  Cities
#   DECIMAL(4,2)/(5,2)     5  1570 m    1.0 mi  Cities
#   SMALLINT scaled        4   682 m    0.4 mi  Cities
#   Deg*10000 (MEDIUMINT)  6    16 m     52 ft  Houses/Businesses
#   DECIMAL(6,4)/(7,4)     7    16 m     52 ft  Houses/Businesses
#   MEDIUMINT scaled       6   2.7 m    8.8 ft  Vehicles
#   FLOAT                  8   1.7 m    5.6 ft  Vehicles
#   DECIMAL(8,6)/(9,6)     9    16cm    1/2 ft  Friends in a mall
#   Deg*10000000 (INT)     8    16mm    5/8 in  Marbles
#   DOUBLE                16   3.5nm     ...    Fleas on a dog
#   POINT (Spatial)       25   3.5nm     ...    (2 doubles, plus overhead)

// think that decimal[cm] data type is enought

function extension_install_geolocation()
{
    $commonObject = new ExtensionCommon;

    $commonObject -> sqlQuery(
        "CREATE TABLE geolocation (
        ID INTEGER NOT NULL AUTO_INCREMENT, 
        IP VARCHAR(12) DEFAULT NULL , 
        COUNTRY VARCHAR(120) DEFAULT NULL,
        COUNTRYCODE VARCHAR(5) DEFAULT NULL,  
        REGION VARCHAR(5) DEFAULT NULL,
        REGIONNAME VARCHAR(120) DEFAULT NULL,
        CITY VARCHAR(120) DEFAULT NULL,
        ZIP INTEGER DEFAULT NULL, 
        LATITUDE DECIMAL(8,6) DEFAULT NULL,
        LONGITUDE DECIMAL(9,6) DEFAULT NULL,
        TIMEZONE VARCHAR(120) DEFAULT NULL,
        ISP VARCHAR(180) DEFAULT NULL,
        ORG VARCHAR(180) DEFAULT NULL,
        ASLABEL VARCHAR(120) DEFAULT NULL,
        OSMAP VARCHAR(2083) DEFAULT NULL,
        GOOGLE VARCHAR(2083) DEFAULT NULL,
        BING VARCHAR(2083) DEFAULT NULL,
        OSMOSE VARCHAR(2083) DEFAULT NULL,
        HERE VARCHAR(2083) DEFAULT NULL,
        SERVER_ONE VARCHAR(2083) DEFAULT NULL,
        SERVER_TWO VARCHAR(2083) DEFAULT NULL,
        PRIMARY KEY (ID)) ENGINE=INNODB;"
    );
}

/** NOTE about url store 
   Discussion => https://stackoverflow.com/questions/219569/best-database-field-type-for-a-url
    Which datatype is better for storing and url encode 
    text mysql < 5
    varchar(max) -- SQLSERVER
    varchar(2083) mysql > 5
    varchar(65535) 
    VARCHAR(512) CHARACTER SET 'ascii' COLLATE 'ascii_general_ci' NOT NULL
 */


/**
 * This function is called on removal and is used to 
 * destroy database schema for the plugin
 */
function extension_delete_geolocation()
{
    $commonObject = new ExtensionCommon;
    $commonObject -> sqlQuery("DROP TABLE IF EXISTS `geolocation`");
}

/**
 * This function is called on plugin upgrade
 */
function extension_upgrade_geolocation()
{

}

?>
