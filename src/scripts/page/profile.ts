import { __, __e, __c, __delay, __report, __toggleStatus } from "@scripts/utils"

export default class ProfileClass {

	eControls: [HTMLSpanElement] | null = null
	
	state = {
		on: { title: 'Approved', msg: 'Do you want to APPROVE this affiliate?'},
		off: { title: 'Disapproved', msg: 'Do you want to DISAPPROVE this affiliate?'}
	}

	constructor() {
		this.eControls = __('.control span', true)
	}

	init() {
		this.eControls?.forEach(
			(a: any) => a.onclick =
				(e: any) => {
					const res = __toggleStatus(e, this.state)
					console.log("ToggleStatus", res)
				}
		)
	}
}