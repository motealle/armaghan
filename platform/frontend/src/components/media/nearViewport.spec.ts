import { afterEach, expect, it, vi } from 'vitest'
import { whenNearViewport } from './nearViewport'

afterEach(()=>vi.unstubAllGlobals())
it('shares one observer, loads only intersecting cards, and updates the 30% height margin',()=>{
  const instances:Array<{callback:(entries:Array<{target:Element;isIntersecting:boolean}>)=>void;options:IntersectionObserverInit;observe:ReturnType<typeof vi.fn>;unobserve:ReturnType<typeof vi.fn>;disconnect:ReturnType<typeof vi.fn>}>=[]
  class Observer {
    observe=vi.fn();unobserve=vi.fn();disconnect=vi.fn()
    constructor(public callback:(entries:Array<{target:Element;isIntersecting:boolean}>)=>void,public options:IntersectionObserverInit){instances.push(this)}
  }
  const listeners=new Map<string,()=>void>()
  const win={innerHeight:800,IntersectionObserver:Observer,addEventListener:vi.fn((event:string,cb:()=>void)=>listeners.set(event,cb)),removeEventListener:vi.fn()}
  vi.stubGlobal('window',win);vi.stubGlobal('IntersectionObserver',Observer)
  const a={} as Element,b={} as Element,loadA=vi.fn(),loadB=vi.fn()
  const stopA=whenNearViewport(a,loadA),stopB=whenNearViewport(b,loadB)
  expect(instances).toHaveLength(1)
  expect(instances[0]!.options.rootMargin).toBe('240px 0px 240px 0px')
  instances[0]!.callback([{target:a,isIntersecting:true},{target:b,isIntersecting:false}])
  expect(loadA).toHaveBeenCalledOnce();expect(loadB).not.toHaveBeenCalled()
  win.innerHeight=1000;listeners.get('resize')!()
  expect(instances[1]!.options.rootMargin).toBe('300px 0px 300px 0px')
  stopA();stopB()
  expect(win.removeEventListener).toHaveBeenCalledWith('resize',expect.any(Function))
})
