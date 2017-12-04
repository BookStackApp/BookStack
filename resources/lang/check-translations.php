<?php 

if( isset( $argv[ 1 ] ) && is_dir( $argv[ 1 ] ) ){

    $dirHandler = opendir( __dir__ . DIRECTORY_SEPARATOR .$argv[1] );
    $missingKeysTotal = array();
    $missingTranslationsTotal = array();
    $leftOverTranslationsTotal = array();

    while ( $filename = readdir( $dirHandler ) ){
        if( strpos( $filename, '.php') ){
            $filenameNoExtension = basename( $filename, '.php' ); 
            $originalArray = include 'en'. DIRECTORY_SEPARATOR . $filename; 
            $translationArray = include $argv[1]. DIRECTORY_SEPARATOR . $filename;
            
            $originalArrayFlatten = flattenArray( $originalArray, $filenameNoExtension );
            $translationArrayFlatten = flattenArray( $translationArray, $filenameNoExtension );

            $missingKeysTotal = array_merge( $missingKeysTotal, getMissingKeys( $originalArrayFlatten, $translationArrayFlatten ));
            $missingTranslationsTotal = array_merge( $missingTranslationsTotal, getMissingTranslations( $originalArrayFlatten, $translationArrayFlatten ) );
            $leftOverTranslationsTotal = array_merge( $leftOverTranslationsTotal, getLeftOverTranslations( $translationArrayFlatten, $originalArrayFlatten ) );
        }  
    }
    
    if( ( $countMissingKeysTotal = count( $missingKeysTotal) ) > 0 )
    {
        echo PHP_EOL."$countMissingKeysTotal translations pending to define".PHP_EOL;
        printArrayKeys( $missingKeysTotal );
    }
    if( ( $countMissingTranslationsTotal = count( $missingTranslationsTotal) ) > 0 )
    {
        echo PHP_EOL."$countMissingTranslationsTotal items defined but pending for a translation".PHP_EOL;
        printArrayKeys( $missingTranslationsTotal );
    }
    if( ( $countLeftOverTranslationsTotal = count( $leftOverTranslationsTotal) ) > 0 )
    {
        echo PHP_EOL."$countLeftOverTranslationsTotal items not longer needed".PHP_EOL;
        printArrayKeys( $leftOverTranslationsTotal );
    } 

}else{
    echo "Not translation available $argv[1]";
}

function flattenArray( array $originalArray, $currentDotKey = '' )
{
    $dotArray = array();
    foreach( $originalArray as $keyOriginalArray=>$valueOriginalArray ){
        if( is_array( $valueOriginalArray ) ){
            $dotArray = array_merge( $dotArray, flattenArray( $valueOriginalArray, calculateDotKey( $keyOriginalArray, $currentDotKey ) ) );
        }else{
            $dotArray[ calculateDotKey( $keyOriginalArray, $currentDotKey ) ] = $valueOriginalArray;
        }
    }
    return $dotArray;
}

function calculateDotKey( $newKey, $currentDotKey='' )

{
    if( $currentDotKey === '' ){
        return $newKey;
    }
    return $currentDotKey. '.' . $newKey;
}

function getMissingKeys( array $originalArray, array $translations  )
{
    return array_diff_key( $originalArray, $translations );
}

function getMissingTranslations( array $originalArray, array $translations  )
{
    return array_intersect_assoc( $originalArray, $translations );
}

function getLeftOverTranslations( array $translations, $originalArray )
{
    return array_intersect_assoc( $translations, $originalArray );
}

function printArrayKeys( array $array )
{
    foreach( $array as $key=>$element)
    {
        echo $key."\n";
    }
}
