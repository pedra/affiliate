import { __, __e, __c, __delay, __report, __toggleStatus } from "@scripts/utils"

export default class JoinClass {

	eLink:  HTMLInputElement | null = null

	eName: HTMLHeadElement | null = null
	eAvatar: HTMLImageElement | null = null
	eProjects: [HTMLSpanElement] | null = null

	eFormCountry: HTMLInputElement | null = null

	user = []
	state = {
		on: { title: 'I want' },
		off: { title: 'No thanks' }
	}

	constructor(){
		this.eLink = __('#aft-link')
		this.eName = __('#aft-name')
		this.eAvatar = __('#aft-avatar')
		this.eProjects = __('.selector span', true)

		this.eFormCountry = __('#form-country')
	}

	async init() {
		this.eProjects?.forEach((a: any) => __e((e: any) => __toggleStatus(e, this.state), a))
		__e((e: any) => this.setCountry(e), this.eFormCountry, 'keyup')
		
		const frm = new FormData()

		try{
		//@ts-ignore
		frm.append('link', this.eLink.value)

		const f = await fetch('/profile', {
			method: 'POST',
			body: frm
		})

		console.log('RES', f)

		const res = await f.json()
		if (res && res.data) {
			console.log("Res:", res.data)
			// @ts-ignore
			this.eName.innerText = res.data.name
			this.eAvatar?.setAttribute('src', `/img/u/${res.data.id}.jpg`) 
			return
		}

		return location.href = '/'

		}catch(e){ console.error(e) }
	}

	async setCountry(e: any) {
		const f = await fetch('http://localhost/a/country/br')
		const j = await f.json()

		console.log('Country:', j)
		if(!j.data || j.data.length == 0) return false

		console.log('Country:', j.data)		
	}


}