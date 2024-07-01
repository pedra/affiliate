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
	}

	async init() {
		this.aftName = __('#aft-name').value
		this.aftId = __('#aft-id').value
		this.aftCode = __('#aft-code').value

		this.eAvatar = __('#aft-avatar')
		this.eProjects = __('.selector span', true)

		this.eFormCountry = __('#form-country')
		this.eSubmit = __('#form-submit')

		this.eProjects?.forEach((a: any) => __e((e: any) => __toggleStatus(e, this.state), a))
		this.observeCountry()
		__e(()=>this.submit(), this.eSubmit)
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

		try{
			const f = await fetch('/a/submit', { method: 'POST', body: frm })
			const j = await f.json()
			if(j && j.error === false && j.data && j.data.error === false){
				__glass(false)
				return this.pageCode(j.data)
			}

			if(j && j.data && j.data.msg) {
				__glass(false)
				__report(j.data.msg)
			}

		}catch(e){}

		__glass(false)
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
		try{
			const f = await fetch('/a/countries')
			const j = await f.json()
		
			if(j && j.error == false && j.data){
				this.countries = j.data
				return true
			}
		} catch(e){}
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

	pageCode(data: any) {
		__('main .container').innerHTML = ''

		const h1 = __c('h1', {}, 'Thank you for registering!')
		const p1 = __c('p', {}, 'Your personal sharing link is: <b>' + data.link + ' </b>')
		const p2 = __c('p', {}, '-- Share it all over the planet!')
		const p3 = __c('p', {}, 'We have sent a verification code to your email.<br>Enter the code below:')

		const ip = __c('input', {id: 'verify-code', placeholder: '999999'})
		const bt = __c('button', {}, 'Verify')
		const ci = __c('div', { class: "code" })
		ci.append(ip, bt)

		const jv = __c('div', { class: "join-verify" })
		jv.append(h1, p1, p2, p3, ci)
		 __('.container').append(jv)

		__e(() => this.verify(), bt)
	}

	async verify() {

		const code = __('#verify-code').value ?? ''

		const frm = new FormData()
		frm.append('code', code)

		__glass()
		try{
			const f = await fetch('/a/verify', {
				method: 'POST',
				body: frm
			})

			if (f.status != 200) {
				__glass(false)
				return __report('Verification failed!<br>Try again.')
			}

			const j = await f.json()
			if(	j && 
				j.error == false && 
				j.data && 
				j.data.verified == true ){
				__glass(false)
				return this.pageLogin(code)
			}
		} catch(e){}

		__glass(false)
		__report('Invalid code!<br>Try again...')
	}

	pageLogin(code:string){
		__('main .container').innerHTML = ''

		const h1 = __c('h1', {}, 'Verified successfully!')
		const h2 = __c('h2', {}, 'Sign in now!')

		// form login
		const e = __c('div', { class: "input" }, __c('label', {}, 'E-mail'))
		e.append(__c('input', { type: "email", id: "sgn-email", placeholder: "E-mail" }))

		const p = __c('div', { class: "input" }, __c('label', {}, 'Password'))
		p.append(__c('input', { type: "password", id: "sgn-password", placeholder: "Password" }))

		const button = __c('button', { id: "sgn-btn-login" }, 'Login')
		__e(() => this.login(code), button)

		const f = __c('div', { class: "form" })
		f.append(e, p, button)

		const jv = __c('div', { class: "join-verify", id: "join-verify" })
		jv.append(h1, h2, f)
		__('.container').append(jv)	
	}

	async login(code:string) {

		const email = __('#sgn-email').value ?? ''
		const password = __('#sgn-password').value ?? ''

		if (email == '' || password == '') return __report('Please, fill all fields')
		const frm = new FormData()
		frm.append('email', email)
		frm.append('password', password)
		frm.append('verification_key', code)

		__glass()
		try{
			const f = await fetch('/login', {
				method: 'POST',
				body: frm
			})

			if (f.status != 200) {
				__glass(false)
				return __report('Login failed!')
			}

			const res = await f.json()
			if (res && res.data && res.data.id && res.data.name) {
				location.href = '/profile'
			}
		} catch(e){}

		__glass(false)
		return __report('Login failed!')
	}
}