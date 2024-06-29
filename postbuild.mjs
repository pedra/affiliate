import { readFileSync, writeFileSync, existsSync, rmdirSync } from 'node:fs'
// import astroConfig from './astro.config.mjs'

const PUBLIC_DIR = "app/public"
const TEMPLATE_DIR = "app/template/page"
const PAGES = ['join', 'profile', 'check', 'typecode', 'verified']

/*
	1 - Search for "PAGES" in PUBLIC_DIR
	
	2 - Read the content of 'PAGES[<actual>]/index.html'
	
	3 - Replace all occurrences of [[...]] with <?php echo ... ?>

	4 - Write the content in 'TEMPLATE_DIR/PAGES[<actual>].php'
	
	5 - Delete PUBLIC_DIR/<PAGES[<actual>]>.

*/
PAGES.forEach(page => {
	const dir = `${PUBLIC_DIR}/${page}`
	if (existsSync(dir)) {
		let content = readFileSync(`${dir}/index.html`, 'utf-8')
		content = String(content).replaceAll(/(\[\[(.*?)\]\])/g, e => e.replace('[[', '<?=$').replace(']]', '?>'))
		writeFileSync(`${TEMPLATE_DIR}/${page}.php`, content)

		// delete original directory
		rmdirSync(dir, { recursive: true })
	}
})

process.exit(0)