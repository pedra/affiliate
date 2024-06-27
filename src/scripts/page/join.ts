import { __, __e, __c, __delay, __glass, __report, __toggleStatus } from "@scripts/utils"

export default class JoinClass {

	eAvatar: HTMLImageElement | null = null
	eProjects: [HTMLSpanElement] | null = null
	
	eFormCountry: HTMLInputElement | null = null
	eSubmit: HTMLButtonElement | null = null
	
	aftName: string | null = null
	aftId: string | null = null
	aftCode: string | null = null

	user = []
	state = {
		on: { title: 'I want' },
		off: { title: 'No thanks' }
	}
	countries = []

	constructor() {
		this.getCountries()
		this.aftName = __('#aft-name').value		
		this.aftId = __('#aft-id').value
		this.aftCode = __('#aft-code').value

		this.eAvatar = __('#aft-avatar')
		this.eProjects = __('.selector span', true)

		this.eFormCountry = __('#form-country')
		this.eSubmit = __('#form-submit')
	}

	async init() {
		this.eProjects?.forEach((a: any) => __e((e: any) => __toggleStatus(e, this.state), a))
		this.observeCountry()
		__e(()=>this.submit(), this.eSubmit)
		//__glass()
	}

	async submit() {
		__glass()
		const data = this.validate()
		console.log('Submit Validade', data )
		if (data === false) return __glass(false)

		this.eProjects?.forEach((a: any) => { if(a.dataset.status == '1') data.projects.push(a.dataset.name) })
		console.log('Data in Submit', data)
		data.projects = data.projects.join(',')
		
		if (data.projects.length == 0 || data.projects.indexOf('around') == -1) {
			__glass(false)
			return __report('Please select at least one project')
		}
		
		const frm = new FormData()
		for(let i in data){
			frm.append(i, data[i])
		}

		const f = await fetch('http://localhost/a/submit', { method: 'POST', body: frm })
		const j = await f.json()
		if(j && j.error === false && j.data && j.data.error === false){

			__report('TODO: Criar o modal/página para a verificação e com o link criado.', 'warn')
			__glass(false)
			return __report('Thank you for your registration!<br>Your link is:<br><b>' + j.data.link + '</b>', 'info')
		}
		__glass(false)
		__report(j.data.msg)
		__report('Please try again later.', 'info')

	}

	validate(): any | boolean {
		const name = __('#form-name')
		const email = __('#form-email')
		const password = __('#form-password')
		const cpassword = __('#form-cpassword')
		const country = __('#form-country')
		const phone = __('#form-phone')
		const company = __('#form-company')

		if (!name.value) {
			__report('Please enter your name')
			return false
		}
		if (!email.value) {
			__report('Please enter your email')
			return false
		}

		if (!password.value) {
			__report('Please enter your password')
			return false
		}
		if (password.value.length < 6) {
			__report('Your password is longer than 6 characters')
			return false
		}
		if (!cpassword.value) {
			__report('Please confirm your password')
			return false
		}
		if (cpassword.value !== password.value) {
			__report('Passwords do not match')
			return false
		}

		if (!country.value) {
			__report('Please select your country')
			return false
		}
		if (country.dataset.id == "") {
			__report('Please select your country')
			return false
		}

		if (!phone.value || phone.value.length < 6) {
			__report('Please enter your phone number')
			return false
		}

		if (!company.value) {
			__report('Please enter your company')
			return false
		}

		return {
			affiliate: this.aftId,
			name: name.value,
			email: email.value,
			password: password.value,
			country: country.dataset.id,
			phone: phone.value,
			company: company.value,
			projects: []
		}
	}

	async getCountries(){
		const f = await fetch('http://localhost/a/countries')
		const j = await f.json()
	
		if(j && j.error == false && j.data){
			this.countries = j.data
			return true
		}
		return false
	}


	async observeCountry() {
		__('#form-country').onchange = (e: any) => {
			let c = e.currentTarget
			let t = false
			this.countries.map((a: any) => {
				if (a.name == c.value) {
					c.dataset.id = a.id
					t = true
				}
			})
			if (!t) {
				c.value = ""
				c.dataset.id = ""
			}
		}
	}


}