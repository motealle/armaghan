export function inputDigits(value:string){return value.replace(/[۰-۹٠-٩]/g,d=>String('۰۱۲۳۴۵۶۷۸۹'.includes(d)?'۰۱۲۳۴۵۶۷۸۹'.indexOf(d):'٠١٢٣٤٥٦٧٨٩'.indexOf(d)))}
export function phoneIdentifier(value:string,dialCode:string){
 const typed=inputDigits(value.trim()).replace(/[\s().-]+/g,'')
 // Existing email/username users remain compatible with the same sign-in field.
 if(/[^+0-9]/.test(typed))return value.trim()
 const dial=inputDigits(dialCode).replace(/[^0-9]/g,'')
 if(typed.startsWith('+'))return typed.replace(/^(\+(?:964|98|90|971))0(?=\d)/,'$1')
 if(typed.startsWith('00'))return '+'+typed.slice(2)
 if(typed.startsWith(dial)&&typed.length>10)return '+'+typed
 return '+'+dial+typed.replace(/^0+/,'')
}
