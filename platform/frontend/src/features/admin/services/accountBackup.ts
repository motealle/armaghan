// Verify the complete server backup before exposing deletion confirmation.
export async function verifyAccountBackup(text:string,expectedHash:string):Promise<void>{
  const hash=await crypto.subtle.digest('SHA-256',new TextEncoder().encode(text))
  const actual=Array.from(new Uint8Array(hash),byte=>byte.toString(16).padStart(2,'0')).join('')
  if(actual!==expectedHash)throw new Error('Incomplete account backup.')
}
