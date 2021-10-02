import Vue from 'vue'
import { ValidationProvider, ValidationObserver, extend, configure } from 'vee-validate';

// extend only the rules we need for a smaller bundle size
// eslint-disable-next-line
import { required, numeric, min, max, email, integer, max_value, min_value } from 'vee-validate/dist/rules';

const rules = { required, numeric, min, max, email, integer, max_value, min_value }
for (const r in rules) {
  extend(r, { ...rules[r], message: 'This field is required' })
}

extend('date', {
  message: field => `The ${field} must follow the format: mm/dd/yyyy`,
  validate: value => ! isNaN(Date.parse(value)),
})

extend('url', {
    message: field => `Url must be valid.`,
    validate: function (value) {
        let regex = new RegExp("^(http[s]?:\\/\\/(www\\.)?|ftp:\\/\\/(www\\.)?|www\\.){1}([0-9A-Za-z-\\.@:%_\+~#=]+)+((\\.[a-zA-Z]{2,3})+)(/(.)*)?(\\?(.)*)?");
        if (regex.test(value)) {
            return true;
        }
        return false;
    }
  })

extend('youtube', {
    message: field => `must be a valid youtube url.`,
    validate: value => value.match(/(youtu\.be|youtube\.com)/) != null ? true : false
  })

  extend('facebook', {
    message: field => `must be a valid facebook url.`,
    validate: value => value.match(/facebook.com/) != null || value.match(/fb.watch/) ? true : false
  })

configure({
  classes: {
    invalid: 'is-invalid',
    valid: 'is-valid',
  },
})

Vue.component('validation-provider', ValidationProvider)
Vue.component('validation-observer', ValidationObserver)
