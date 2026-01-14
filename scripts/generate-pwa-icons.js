#!/usr/bin/env node
/**
 * PWA Icons Generator for Paynes
 *
 * Generates all required PWA icons from the source SVG.
 *
 * Requirements:
 *   npm install sharp
 *
 * Usage:
 *   node scripts/generate-pwa-icons.js
 */

const sharp = require('sharp');
const fs = require('fs');
const path = require('path');

const SOURCE_SVG = path.join(__dirname, '../public/icons/icon.svg');
const OUTPUT_DIR = path.join(__dirname, '../public/icons');

// Icon sizes for PWA
const ICON_SIZES = [72, 96, 128, 144, 152, 180, 192, 384, 512];

// Splash screen sizes for iOS
const SPLASH_SIZES = [
  { width: 640, height: 1136, name: 'splash-640x1136' },
  { width: 750, height: 1334, name: 'splash-750x1334' },
  { width: 1242, height: 2208, name: 'splash-1242x2208' },
  { width: 1125, height: 2436, name: 'splash-1125x2436' },
  { width: 1536, height: 2048, name: 'splash-1536x2048' },
  { width: 1668, height: 2224, name: 'splash-1668x2224' },
  { width: 2048, height: 2732, name: 'splash-2048x2732' },
];

async function generateIcons() {
  console.log('Generating PWA icons...\n');

  // Ensure output directory exists
  if (!fs.existsSync(OUTPUT_DIR)) {
    fs.mkdirSync(OUTPUT_DIR, { recursive: true });
  }

  const svgBuffer = fs.readFileSync(SOURCE_SVG);

  // Generate app icons
  for (const size of ICON_SIZES) {
    const outputPath = path.join(OUTPUT_DIR, `icon-${size}x${size}.png`);

    await sharp(svgBuffer)
      .resize(size, size)
      .png()
      .toFile(outputPath);

    console.log(`Created: icon-${size}x${size}.png`);
  }

  // Generate badge icon (smaller, for notifications)
  await sharp(svgBuffer)
    .resize(72, 72)
    .png()
    .toFile(path.join(OUTPUT_DIR, 'badge-72x72.png'));
  console.log('Created: badge-72x72.png');

  // Generate favicon PNGs
  for (const size of [16, 32]) {
    await sharp(svgBuffer)
      .resize(size, size)
      .png()
      .toFile(path.join(__dirname, `../public/favicon-${size}x${size}.png`));
    console.log(`Created: favicon-${size}x${size}.png`);
  }

  // Generate apple-touch-icon
  await sharp(svgBuffer)
    .resize(180, 180)
    .png()
    .toFile(path.join(__dirname, '../public/apple-touch-icon.png'));
  console.log('Created: apple-touch-icon.png');

  console.log('\nGenerating splash screens...\n');

  // Generate splash screens
  for (const splash of SPLASH_SIZES) {
    const outputPath = path.join(OUTPUT_DIR, `${splash.name}.png`);

    // Create splash screen with centered icon
    const iconSize = Math.min(splash.width, splash.height) * 0.3;

    await sharp({
      create: {
        width: splash.width,
        height: splash.height,
        channels: 4,
        background: { r: 26, g: 119, b: 201, alpha: 1 } // #1A77C9
      }
    })
      .composite([
        {
          input: await sharp(svgBuffer)
            .resize(Math.round(iconSize), Math.round(iconSize))
            .toBuffer(),
          gravity: 'center'
        }
      ])
      .png()
      .toFile(outputPath);

    console.log(`Created: ${splash.name}.png`);
  }

  // Generate shortcut icons
  console.log('\nGenerating shortcut icons...\n');

  const shortcutIcons = [
    { name: 'shortcut-payment', emoji: '💳' },
    { name: 'shortcut-exchange', emoji: '💱' },
    { name: 'shortcut-shift', emoji: '⏱️' },
  ];

  // For shortcut icons, we'll just use the main icon resized
  for (const shortcut of shortcutIcons) {
    await sharp(svgBuffer)
      .resize(96, 96)
      .png()
      .toFile(path.join(OUTPUT_DIR, `${shortcut.name}.png`));
    console.log(`Created: ${shortcut.name}.png`);
  }

  console.log('\n✅ All icons generated successfully!');
  console.log(`\nOutput directory: ${OUTPUT_DIR}`);
}

// Run if called directly
if (require.main === module) {
  generateIcons().catch(console.error);
}

module.exports = { generateIcons };
