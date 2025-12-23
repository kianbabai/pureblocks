/**
 * Build script that auto-discovers all blocks and builds them.
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

console.log(`Building ${blocks.length} block(s): ${blocks.join(', ')}`);

const projectRoot = path.join(__dirname, '..');

blocks.forEach((block) => {
	const entry = `src/blocks/${block}/index.js`;
	const outDir = path.join('build', block);
	const command = `wp-scripts build ${entry} --output-path=${outDir}`;
	console.log(`\n→ Building ${block}`);
	execSync(command, { stdio: 'inherit', cwd: projectRoot });

	// Clean up the extra copied block.json tree wp-scripts places under build/{block}/blocks/.
	const copiedBlocksDir = path.join(projectRoot, outDir, 'blocks');
	if (fs.existsSync(copiedBlocksDir)) {
		fs.rmSync(copiedBlocksDir, { recursive: true, force: true });
	}
});

// Clean up any root-level build/blocks/ copies.
const rootBlocksDir = path.join(projectRoot, 'build', 'blocks');
if (fs.existsSync(rootBlocksDir)) {
	fs.rmSync(rootBlocksDir, { recursive: true, force: true });
}

