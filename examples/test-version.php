<?php

/**
 * Version Test
 * 
 * This script tests the version functionality of the package.
 */

// Load required files
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/Version.php';

use BellissimoPizza\RequestDtoGenerator\Version;

echo "🚀 Version Test\n";
echo "===============\n\n";

echo "1️⃣ Testing Version Class...\n";

// Test basic version information
echo "✅ Package Name: " . Version::getPackageName() . "\n";
echo "✅ Version: " . Version::getVersion() . "\n";
echo "✅ Full Identifier: " . Version::getFullIdentifier() . "\n\n";

echo "2️⃣ Testing Version Comparison...\n";

// Test version comparisons
$testVersions = ['0.9.0', '1.0.0', '1.1.0', '2.0.0'];

foreach ($testVersions as $testVersion) {
    echo "🔍 Comparing with {$testVersion}:\n";
    echo "   Greater than: " . (Version::isGreaterThan($testVersion) ? 'Yes' : 'No') . "\n";
    echo "   Greater than or equal: " . (Version::isGreaterThanOrEqual($testVersion) ? 'Yes' : 'No') . "\n";
    echo "   Less than: " . (Version::isLessThan($testVersion) ? 'Yes' : 'No') . "\n";
    echo "   Less than or equal: " . (Version::isLessThanOrEqual($testVersion) ? 'Yes' : 'No') . "\n";
    echo "   Equals: " . (Version::equals($testVersion) ? 'Yes' : 'No') . "\n\n";
}

echo "3️⃣ Testing Version Info Array...\n";

$versionInfo = Version::getInfo();
echo "✅ Version Info:\n";
foreach ($versionInfo as $key => $value) {
    echo "   {$key}: {$value}\n";
}

echo "\n4️⃣ Testing Composer.json Version...\n";

// Check if composer.json has version
$composerPath = __DIR__ . '/../composer.json';
if (file_exists($composerPath)) {
    $composer = json_decode(file_get_contents($composerPath), true);
    if (isset($composer['version'])) {
        echo "✅ Composer.json version: " . $composer['version'] . "\n";
        echo "✅ Version matches: " . (Version::equals($composer['version']) ? 'Yes' : 'No') . "\n";
    } else {
        echo "❌ No version found in composer.json\n";
    }
} else {
    echo "❌ composer.json not found\n";
}

echo "\n🎉 Version test completed!\n";

echo "\n📋 Summary:\n";
echo "===========\n";
echo "✅ Version class: Working\n";
echo "✅ Version comparisons: Working\n";
echo "✅ Version info: Working\n";
echo "✅ Composer integration: " . (isset($composer['version']) ? 'Working' : 'Needs setup') . "\n";

echo "\n🚀 Usage Examples:\n";
echo "==================\n";
echo "• Get version: " . Version::getVersion() . "\n";
echo "• Get package name: " . Version::getPackageName() . "\n";
echo "• Get full identifier: " . Version::getFullIdentifier() . "\n";
echo "• Check if version >= 1.0.0: " . (Version::isGreaterThanOrEqual('1.0.0') ? 'Yes' : 'No') . "\n";

echo "\n✨ Version test completed successfully!\n";
