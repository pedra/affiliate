

// QuerySelector e: element (string: '.class') | a: all elements (boolean: true)
//@ts-ignore
export const __ = (e, a = false) => document[`querySelector${a ? "All" : ""}`](e) || null

// AddEventListener a: action (function) | e: element (string|HTML Node) | v: event type (strng: 'click')
export const __e = (a:any, e:any = 'document', v:string = "click") => {
	let c:any = e != null && 'object' == typeof e ? e :
		(e == 'document' || !e || e == "" || e == null ? document : __(e, true))
	if (c == null || c.length == 0) return false
	return (!c[0] ? [c] : c).forEach((x:any) => x.addEventListener(v, a))
}

// Create Element
export const __c = (type:string = 'div', a:any = {}, content:any = false) => {
	const e = document.createElement(type)
	// @ts-ignore
	for (let x in a) e.setAttribute(x, a[x])
	switch (typeof content) {
		case 'boolean': break
		case 'string': e.innerHTML = content; break
		case 'object': e.appendChild(content); break
	}
	return e
}

// Converte um INTEIRO para a base 36 ou gera um randômico usando a DATA atual (timestamp)
export const __random = (n?: number): string => ('number' == typeof n ? n : new Date().getTime()).toString(36)
export const __randomize = (max: any): number => Math.floor(Math.random() * parseInt(max + 0))

export const __delay = (ms: number) => new Promise(r => setTimeout(r, ms))

export const __glass = (a:boolean = true) => {
	if (a === false) return __('.__glass').classList.remove('on')
	let g = __('.__glass')
	if (!g) {
		let b:any = __c('div', { class: '__glass' }, __c('img', { src: "/asset/img/l.gif", alt: 'loading'}))
		document.body.appendChild(b)
		setTimeout(() => b.classList.add('on'), 200)
		return
	}
	g.classList.add('on')
}

// Mostra mensagem na tela
export const __report = async (
	text: string,
	type: string = '',
	extra: any = null,
	tempo: number | any = null
): Promise<void> => {
	extra = extra || null
	tempo = tempo || 2000 + text.length * 40
	type = '__rep-' + ((type == 'info' || type == 'i') ? "info" : (type == 'warn' || type == 'w') ? "warn" : "alert")

	const id = '__'+__random()
	const c = document.createElement('DIV')
	c.className = `__rep-content ${type}`
	c.id = id
	c.innerHTML = '<span class="material-symbols-outlined pulse __report_i">close</span>' + text
	c.onclick = function (e: any): void {
		const x = e.currentTarget
		x.classList.remove('active')
		__delay(400).then(() => x.remove())
	}

	__('#__report')?.appendChild(c)

	await __delay(500)
	__('#'+id).classList.add('active')
	
	await __delay(tempo)
	const e = __('#' + id)
	if (e) {
		e.classList.remove('active')
		await __delay(400)
		e.remove()
	}
}

export const __toggleStatus = (e: any, state: any): string | boolean => {
	const x = e.currentTarget
	if (x.classList.contains('disabled')) return false

	let a: string = x.innerHTML.replace('toggle_', '') == 'on' ? 'off' : 'on'

	if (state[a].msg) {
		const r = confirm(state[a].msg)
		if (!r) return false
	}

	x.innerHTML = `toggle_${a}`
	x.classList[a == 'off' ? 'add' : 'remove']('off')
	x.dataset.status = a == 'on' ? '1' : '0'

	x.previousElementSibling.innerHTML = state[a].title
	return a
}