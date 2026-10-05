import {expect,it,vi} from 'vitest'
import {webcrypto} from 'node:crypto'
import {verifyAccountBackup} from './accountBackup'
it('requires the complete downloaded backup, including Persian content',async()=>{
  vi.stubGlobal('crypto',webcrypto)
  const text='{"name":"ارمغان"}'
  const digest=Buffer.from(await webcrypto.subtle.digest('SHA-256',new TextEncoder().encode(text))).toString('hex')
  await expect(verifyAccountBackup(text,digest)).resolves.toBeUndefined()
  await expect(verifyAccountBackup(text+' ',digest)).rejects.toThrow('Incomplete account backup.')
  vi.unstubAllGlobals()
})
