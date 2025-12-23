/**
 * Start script that auto-discovers all blocks and starts the dev server.
 */

const { execSync } = require('child_process');
const fs = require('fs');
const path = require('path');

const blocksDir = path.join(__dirname, '..', 'src', 'blocks');
const blocks = fs.readdirSync(blocksDir, { withFileTypes: true })
	.filter(dirent => dirent.isDirectory())
	.map(dirent => dirent.name)
	.filter(name => {
		const blockJsonPath = path.join(blocksDir, name, 'block.json');
		return fs.existsSync(blockJsonPath);
	});

if (blocks.length === 0) {
	console.error('No blocks found in src/blocks/');
	process.exit(1);
}

const projectRoot = path.join(__dirname, '..');

// Watch all blocks at once so changes rebuild automatically.
const entries = blocks.map((block) => `src/blocks/${block}/index.js`).join(' ');
const command = `wp-scripts start ${entries} --output-path=build`;

console.log(`Starting dev server for blocks: ${blocks.join(', ')}`);
execSync(command, { stdio: 'inherit', cwd: projectRoot });

