import { __, __e, __glass, __report } from "@scripts/utils"

export default class JoinClass {

	constructor() {}

	async init() {
		__e(() => this.login(), __('.form button'))
		
		alert('check 👍')
	}

	async login() {
		const email = __('#sgn-email').value ?? ''
		const password = __('#sgn-password').value ?? ''
		
		if (email == '' || password == '') 
			return __report('Please, fill all fields')
		
		__glass()
		const frm = new FormData()
		frm.append('email', email)
		frm.append('password', password)
		
		const f = await fetch('/login', {
			method: 'POST',
			body: frm
		})

		// if (f.status != 200) {
		// 	__glass(false)
		// 	return __report('Login failed!')
		// }

		const res = await f.json()
		if (res && res.data && res.data.id && res.data.name)
			location.href = '/profile'

		__glass(false)
		return __report('Login failed!')
	}
}