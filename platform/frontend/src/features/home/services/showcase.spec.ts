import {it,expect} from 'vitest'
import {emptyVisualStyleProfile} from '@/features/visual-editor/store'
import {capabilityDetails,isShown,aparatEmbed,localVideo,videoEmbed,videoFile,clips} from './showcase'
it('hides exactly requested capability details by default and keeps explicit overrides reversible',()=>{
 const p=emptyVisualStyleProfile()
 for(const [group,visible] of [['production',[2,3,5,6,7]],['export',[1,2,7,8]],['trade',[2]]] as const){
  expect(capabilityDetails.filter(d=>d.group===group&&isShown(p,d.id,d.defaultHidden)).map(d=>d.index)).toEqual(visible)
 }
 const id='home.capability.production.detail.1';p.styles[id]={hidden:false};expect(isShown(p,id,true)).toBe(true)
})
it('provides three locale-independent files and validates Aparat/local sources without embedding arbitrary HTML or URLs',()=>{
 const p=emptyVisualStyleProfile();expect(clips.map(c=>videoFile(p,c.id,c.file))).toEqual(['/vids/farsi.mp4','/vids/arabic.mp4','/vids/kordi.mp4'])
 expect(aparatEmbed('https://www.aparat.com/v/abc123')).toBe('https://www.aparat.com/video/video/embed/videohash/abc123/vt/frame')
 for(const url of ['javascript:alert(1)','https://aparat.com.evil.test/v/abc','https://user@aparat.com/v/abc','https://aparat.com:444/v/abc','<iframe src=x>'])expect(aparatEmbed(url)).toBeNull()
 expect(localVideo('/vids/../private.mp4')).toBeNull();expect(localVideo('https://evil.test/file.mp4')).toBeNull()
 p.texts['home.videos.aparat']={ar:'https://aparat.com/v/abc123'};expect(videoEmbed(p,'ar')).toBeNull()
 p.styles['home.videos.ar.aparat']={hidden:false};expect(videoEmbed(p,'ar')).toContain('abc123')
})
