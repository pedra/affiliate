import { __, __e, __c, __delay, __report, __toggleStatus } from "@scripts/utils"

export default class JoinClass {

	eLink:  HTMLInputElement | null = null

	eName: HTMLHeadElement | null = null
	eAvatar: HTMLImageElement | null = null
	eProjects: [HTMLSpanElement] | null = null

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
	}

	async init() {
		this.eProjects?.forEach((a: any) => a.onclick =	(e: any) => __toggleStatus(e, this.state))
		
		const frm = new FormData()
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
	}


}