#!/usr/bin/env node
/*
Small CLI to extract text from a PDF using pdf-parse.
Usage:
  node scripts/extract-pdf-text.cjs "input.pdf" "output.txt"
*/

const fs = require('fs');
const path = require('path');
const pdf = require('pdf-parse');

async function main() {
  const [input, output] = process.argv.slice(2);
  if (!input || !output) {
    console.error('Usage: node scripts/extract-pdf-text.cjs "input.pdf" "output.txt"');
    process.exit(1);
  }

  const inputPath = path.resolve(process.cwd(), input);
  const outputPath = path.resolve(process.cwd(), output);

  if (!fs.existsSync(inputPath)) {
    console.error(`Input file not found: ${inputPath}`);
    process.exit(1);
  }

  const dataBuffer = fs.readFileSync(inputPath);
  try {
    const data = await pdf(dataBuffer);
    fs.mkdirSync(path.dirname(outputPath), { recursive: true });
    fs.writeFileSync(outputPath, data.text, 'utf8');
    console.log(`Extracted ${data.text.length.toLocaleString()} chars to ${outputPath}`);
  } catch (err) {
    console.error('Failed to parse PDF:', err.message || err);
    process.exit(1);
  }
}

main();
