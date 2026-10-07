import {expect,it,vi} from 'vitest'
import {acquirePhotoSlot} from './photoRequestQueue'
it('starts only two photos at once and releases capacity on completion or navigation',()=>{
 const starts=Array.from({length:4},()=>vi.fn())
 const releases=starts.map(start=>acquirePhotoSlot(start))
 expect(starts.map(start=>start.mock.calls.length)).toEqual([1,1,0,0])
 releases[2]!();releases[0]!()
 expect(starts.map(start=>start.mock.calls.length)).toEqual([1,1,0,1])
 releases.forEach(release=>release())
 const next=vi.fn(),stop=acquirePhotoSlot(next)
 expect(next).toHaveBeenCalledOnce();stop()
})
