/**
 * Site forms (contact and "notify me") sent to Contact Form 7 through its REST endpoint.
 *
 * Expected markup (built by the render.php of each block):
 *  - <form data-ci-form="contact" data-endpoint data-form-id data-locale
 *          data-msg-required data-msg-email data-msg-error>
 *  - Fields with a name; [required] and type="email" are validated when the field loses focus.
 *  - Error of each field: <span data-error-for="name" id="…" hidden>.
 *  - Request type (optional): radios name="type-choice" (with data-cta), sent as "type".
 *  - Honeypot: input name="website" (when filled in, the submission is silently dropped).
 *  - General status: [data-ci-status] (role="alert"). Button: [type=submit] with data-sending.
 *  - Success: <template data-ci-success> next to the form, inside [data-ci-form-wrap];
 *    [data-ci-message] receives the Contact Form 7 message and a [data-ci-again] button shows
 *    the form again.
 */

const TYPE_CHOICE = 'type-choice';
const INTERNAL = new Set( [ TYPE_CHOICE, 'website' ] );

/* ── Field errors ─────────────────────────────────────────────── */

function setError( form, name, message ) {
	const field = form.elements[ name ];
	const box = form.querySelector( `[data-error-for="${ name }"]` );
	if ( ! field || ! box ) {
		return;
	}
	const input = field instanceof window.RadioNodeList ? field[ 0 ] : field;
	if ( message ) {
		box.textContent = message;
		box.hidden = false;
		input.setAttribute( 'aria-invalid', 'true' );
		input.setAttribute( 'aria-describedby', box.id );
	} else {
		box.textContent = '';
		box.hidden = true;
		input.removeAttribute( 'aria-invalid' );
		input.removeAttribute( 'aria-describedby' );
	}
}

/**
 * Same rules Contact Form 7 applies on submit.
 *
 * @param {HTMLFormElement} form  Form (messages in data-msg-*).
 * @param {HTMLElement}     input Field.
 * @return {string} Error message, or '' when the value is valid.
 */
function validate( form, input ) {
	const value = input.value.trim();
	if ( input.required && ! value ) {
		return form.dataset.msgRequired;
	}
	if (
		input.type === 'email' &&
		value &&
		! /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test( value )
	) {
		return form.dataset.msgEmail;
	}
	return '';
}

/* ── Submission ───────────────────────────────────────────────── */

async function submit( form ) {
	const data = new FormData( form );
	const body = new FormData();
	for ( const [ name, value ] of data.entries() ) {
		if ( ! INTERNAL.has( name ) ) {
			body.set( name, String( value ).slice( 0, 5000 ) );
		}
	}
	const type = form.querySelector( `input[name="${ TYPE_CHOICE }"]:checked` );
	if ( type ) {
		body.set( 'type', type.value );
	}
	const id = form.dataset.formId;
	body.set( '_wpcf7', id );
	body.set( '_wpcf7_unit_tag', `wpcf7-f${ id }-o1` );
	body.set( '_wpcf7_locale', form.dataset.locale || 'en_US' );

	const res = await window.fetch( form.dataset.endpoint, {
		method: 'POST',
		body,
	} );
	if ( ! res.ok ) {
		throw new Error( String( res.status ) );
	}
	return res.json();
}

function showSuccess( form, message ) {
	const wrap = form.closest( '[data-ci-form-wrap]' );
	const template = wrap?.querySelector( 'template[data-ci-success]' );
	if ( ! wrap || ! template ) {
		return;
	}
	const success = template.content.firstElementChild.cloneNode( true );
	const slot = success.querySelector( '[data-ci-message]' );
	if ( slot && message ) {
		slot.textContent = message;
	}
	form.hidden = true;
	wrap.appendChild( success );
	success.querySelector( '[tabindex="-1"]' )?.focus();
	success
		.querySelector( '[data-ci-again]' )
		?.addEventListener( 'click', () => {
			success.remove();
			form.hidden = false;
			form.querySelector(
				'input:not([type=hidden]):not([type=radio]), textarea'
			)?.focus();
		} );
}

function initForm( form ) {
	const status = form.querySelector( '[data-ci-status]' );
	const button = form.querySelector( '[type="submit"]' );
	const buttonLabel = button?.textContent;
	form.noValidate = true;

	// Service preselected from ?service=slug (the quote button of each service).
	const slug = new URLSearchParams( window.location.search ).get( 'service' );
	const select = form.querySelector( 'select[name="service"]' );
	const option =
		slug &&
		select?.querySelector(
			`option[data-slug="${ window.CSS.escape( slug ) }"]`
		);
	if ( option ) {
		select.value = option.value;
	}

	// The button text follows the chosen request type.
	form.querySelectorAll( `input[name="${ TYPE_CHOICE }"]` ).forEach(
		( radio ) =>
			radio.addEventListener( 'change', () => {
				if ( radio.checked && radio.dataset.cta && button ) {
					button.textContent = radio.dataset.cta;
				}
			} )
	);

	form.addEventListener(
		'blur',
		( event ) => {
			const input = event.target;
			if (
				input.name &&
				! INTERNAL.has( input.name ) &&
				form.querySelector( `[data-error-for="${ input.name }"]` )
			) {
				setError( form, input.name, validate( form, input ) );
			}
		},
		true
	);

	form.addEventListener( 'submit', async ( event ) => {
		event.preventDefault();
		if ( status ) {
			status.textContent = '';
		}

		// Client-side validation before sending.
		let first = null;
		form.querySelectorAll( '[data-error-for]' ).forEach( ( box ) => {
			const input = form.elements[ box.dataset.errorFor ];
			if ( input && ! ( input instanceof window.RadioNodeList ) ) {
				const message = validate( form, input );
				setError( form, input.name, message );
				first ??= message ? input : null;
			}
		} );
		if ( first ) {
			first.focus();
			return;
		}

		if ( button ) {
			button.disabled = true;
			button.textContent = button.dataset.sending || buttonLabel;
		}
		const isBot =
			String( new FormData( form ).get( 'website' ) ?? '' ) !== '';

		try {
			const json = isBot ? { status: 'mail_sent' } : await submit( form );
			if ( json.status === 'mail_sent' ) {
				if ( ! isBot ) {
					const type = form.querySelector(
						`input[name="${ TYPE_CHOICE }"]:checked`
					);
					window.gtag?.( 'event', 'generate_lead', {
						form: form.dataset.ciForm,
						...( type ? { type: type.value } : {} ),
					} );
				}
				form.reset();
				showSuccess( form, json.message );
			} else {
				let firstInvalid = null;
				( json.invalid_fields || [] ).forEach( ( field ) => {
					setError( form, field.field, field.message );
					firstInvalid ??= form.elements[ field.field ];
				} );
				if ( status ) {
					status.textContent = json.message || form.dataset.msgError;
				}
				( firstInvalid instanceof window.RadioNodeList
					? firstInvalid[ 0 ]
					: firstInvalid
				)?.focus();
			}
		} catch {
			if ( status ) {
				status.textContent = form.dataset.msgError;
			}
		}

		if ( button ) {
			button.disabled = false;
			const checked = form.querySelector(
				`input[name="${ TYPE_CHOICE }"]:checked`
			);
			button.textContent = checked?.dataset.cta || buttonLabel;
		}
	} );
}

document.querySelectorAll( 'form[data-ci-form]' ).forEach( initForm );
