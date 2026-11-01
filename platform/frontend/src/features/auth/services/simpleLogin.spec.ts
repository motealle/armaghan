import {it,expect} from 'vitest'
import {phoneIdentifier} from './simpleLogin'
it('accepts local IQ/IR numbers and all three digit scripts, retains username/email compatibility',()=>{
 expect(phoneIdentifier('٠٧٧٠ ١٢٣ ٤٥٦٧','+964')).toBe('+9647701234567')
 expect(phoneIdentifier('۰۹۱۲۱۲۳۴۵۶۷','+98')).toBe('+989121234567')
 expect(phoneIdentifier('009647701234567','+98')).toBe('+9647701234567')
 expect(phoneIdentifier('+96407701234567','+964')).toBe('+9647701234567')
 expect(phoneIdentifier('shop1','+964')).toBe('shop1')
 expect(phoneIdentifier('a@example.test','+964')).toBe('a@example.test')
})
