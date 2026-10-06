import {afterEach,beforeEach,expect,it,vi} from 'vitest'
import {capturePosition,safePositionKey,storedPosition,waitForPosition} from './browsingPosition'

let ready=false,height=3000
let changed:()=>void
let listeners:Map<string,()=>void>
const route={path:'/products',fullPath:'/products?category=1&subcategory=11'}
beforeEach(()=>{
  ready=false;height=3000;listeners=new Map()
  const memory=new Map<string,string>()
  vi.stubGlobal('localStorage',{getItem:(k:string)=>memory.get(k)??null,setItem:(k:string,v:string)=>memory.set(k,v)})
  vi.stubGlobal('window',{location:{pathname:'/'},scrollX:0,scrollY:700,innerHeight:800,
    addEventListener:(name:string,fn:()=>void)=>listeners.set(name,fn),removeEventListener:(name:string)=>listeners.delete(name)})
  vi.stubGlobal('document',{documentElement:{get scrollHeight(){return height}},querySelector:()=>ready?{}:null})
  class Observer {constructor(cb:()=>void){changed=cb}observe=vi.fn();disconnect=vi.fn()}
  vi.stubGlobal('ResizeObserver',Observer);vi.stubGlobal('MutationObserver',Observer)
  vi.useFakeTimers()
})
afterEach(()=>{vi.useRealTimers();vi.unstubAllGlobals()})

it('restores the stored height after delayed product content becomes ready, without persisting private token routes',async()=>{
  capturePosition(route)
  expect(storedPosition(route)).toEqual({left:0,top:700})
  for(const fullPath of ['/magic/private-token','/s/private-token','/favorites/share/private-token','/admin','/products?token=private'])expect(safePositionKey({path:fullPath.split('?')[0]!,fullPath})).toBeNull()
  const result=vi.fn();const pending=waitForPosition(route,{left:0,top:700}).then(result)
  changed();await Promise.resolve();expect(result).not.toHaveBeenCalled()
  ready=true;changed();await pending
  expect(result).toHaveBeenCalledWith({left:0,top:700})
})
it('does not jump later if the user scrolls while content is loading',async()=>{
  const pending=waitForPosition(route,{left:0,top:700})
  listeners.get('wheel')!()
  ready=true;changed()
  expect(await pending).toBe(false)
  expect(listeners.size).toBe(0)
})
it('clamps when content shrinks and tolerates browser storage restrictions',async()=>{
  height=1100
  const pending=waitForPosition(route,{left:0,top:700})
  await vi.advanceTimersByTimeAsync(20_000)
  expect(await pending).toEqual({left:0,top:300})
  vi.stubGlobal('localStorage',{getItem:()=>{throw Error('blocked')},setItem:()=>{throw Error('blocked')}})
  expect(()=>capturePosition(route)).not.toThrow()
  expect(storedPosition(route)).toBeNull()
})
