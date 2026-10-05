export type VisualTargetKind='section'|'panel'|'title'|'text'|'media'|'control'
export type VisualStyleControl='textColor'|'backgroundColor'|'borderColor'|'imageFit'|'imagePosition'

export interface VisualTargetDefinition{
  id:string
  label:string
  kind:VisualTargetKind
  textEditable?:boolean
  hideable?:boolean
  styleControls?:VisualStyleControl[]
}

export interface VisualTargetGroup{
  id:string
  label:string
  targets:VisualTargetDefinition[]
}

const whyItemTargets:VisualTargetDefinition[]=Array.from({length:4},(_,offset):VisualTargetDefinition[]=>{
  const number=offset+1
  return[
    {id:`home.why.item.${number}`,label:`پنل دلیل ${number}`,kind:'panel'},
    {id:`home.why.item.${number}.title`,label:`عنوان دلیل ${number}`,kind:'title',textEditable:true},
    {id:`home.why.item.${number}.text`,label:`متن دلیل ${number}`,kind:'text',textEditable:true},
  ]
}).flat()

const capabilityLabels={
  production:'تولید',
  export:'آماده‌سازی صادرات',
  trade:'اسناد تجاری',
} as const

const capabilityTargets:VisualTargetDefinition[]=Object.entries(capabilityLabels).flatMap(([id,label])=>[
  {id:`home.capability.${id}`,label:`کارت توانمندی ${label}`,kind:'panel'},
  {id:`home.capability.${id}.media`,label:`تصویر ${label}`,kind:'media',styleControls:['imageFit','imagePosition']},
  {id:`home.capability.${id}.body`,label:`پنل متن ${label}`,kind:'panel'},
  {id:`home.capability.${id}.title`,label:`عنوان ${label}`,kind:'title',textEditable:true},
  {id:`home.capability.${id}.text`,label:`متن ${label}`,kind:'text',textEditable:true},
])

const bannerLabels={'1':'نوزادی','2':'بچگانه','3':'زنانه'} as const
const bannerTargets:VisualTargetDefinition[]=Object.entries(bannerLabels).flatMap(([id,label])=>[
  {id:`home.product-banner.${id}`,label:`بنر ${label}`,kind:'panel'},
  {id:`home.product-banner.${id}.media`,label:`تصویر بنر ${label}`,kind:'media',styleControls:['imageFit','imagePosition']},
  {id:`home.product-banner.${id}.copy`,label:`پنل متن بنر ${label}`,kind:'panel'},
])

export const visualTargetGroups:VisualTargetGroup[]=[
  {
    id:'home',
    label:'صفحه خانه',
    targets:[
      {
        id:'home.page',
        label:'زمینه کل صفحه خانه',
        kind:'section',
        hideable:false,
        styleControls:['backgroundColor'],
      },
      {id:'home.content',label:'محتوای صفحه خانه',kind:'section'},
    ],
  },
  {
    id:'header',
    label:'هدر و برند',
    targets:[
      {id:'header.shell',label:'نوار بالای سایت',kind:'section'},
      {id:'header.brand',label:'بخش لوگو و برند',kind:'panel'},
      {id:'header.brand-name',label:'نام برند',kind:'title',textEditable:true},
      {id:'header.manufacturer',label:'زیرعنوان برند',kind:'text',textEditable:true},
    ],
  },
  {
    id:'hero',
    label:'هیرو',
    targets:[
      {id:'hero.shell',label:'قاب هیرو',kind:'section'},
      {id:'hero.media',label:'تصویر هیرو',kind:'media',styleControls:['imageFit','imagePosition']},
      {id:'hero.caption',label:'پنل متن هیرو',kind:'panel'},
      {id:'hero.title',label:'عنوان هیرو',kind:'title',textEditable:true},
    ],
  },
  {
    id:'about',
    label:'درباره ارمغان',
    targets:[
      {id:'home.about',label:'کل بخش درباره',kind:'section'},
      {id:'home.about.heading',label:'سربرگ درباره',kind:'panel'},
      {id:'home.about.eyebrow',label:'بالانویس درباره',kind:'text',textEditable:true},
      {id:'home.about.title',label:'عنوان درباره',kind:'title',textEditable:true},
      {id:'home.about.copy',label:'پنل متن درباره',kind:'panel'},
      {id:'home.about.text',label:'متن درباره',kind:'text',textEditable:true},
      {id:'home.about.media',label:'تصویر درباره',kind:'media',styleControls:['imageFit','imagePosition']},
    ],
  },
  {
    id:'why',
    label:'چرا ارمغان',
    targets:[
      {id:'home.why',label:'کل بخش چرا ارمغان',kind:'section'},
      {id:'home.why.heading',label:'سربرگ چرا ارمغان',kind:'panel'},
      {id:'home.why.eyebrow',label:'بالانویس چرا ارمغان',kind:'text',textEditable:true},
      {id:'home.why.title',label:'عنوان چرا ارمغان',kind:'title',textEditable:true},
      {id:'home.why.intro',label:'مقدمه چرا ارمغان',kind:'text',textEditable:true},
      {id:'home.why.list',label:'پنل فهرست دلایل',kind:'panel'},
      ...whyItemTargets,
    ],
  },
  {
    id:'capabilities',
    label:'توانمندی‌ها',
    targets:[
      {id:'home.capabilities',label:'کل بخش توانمندی‌ها',kind:'section'},
      {id:'home.capabilities.heading',label:'سربرگ توانمندی‌ها',kind:'panel'},
      {id:'home.capabilities.eyebrow',label:'بالانویس توانمندی‌ها',kind:'text',textEditable:true},
      {id:'home.capabilities.title',label:'عنوان توانمندی‌ها',kind:'title',textEditable:true},
      {id:'home.capabilities.intro',label:'مقدمه توانمندی‌ها',kind:'text',textEditable:true},
      {id:'home.capabilities.grid',label:'شبکه کارت‌های توانمندی',kind:'panel'},
      ...capabilityTargets,
    ],
  },
  {
    id:'banners',
    label:'بنرهای محصول',
    targets:[
      {id:'home.product-banners',label:'کل بخش بنرها',kind:'section'},
      {id:'home.product-banners.heading',label:'سربرگ بنرها',kind:'panel'},
      {id:'home.product-banners.eyebrow',label:'بالانویس بنرها',kind:'text',textEditable:true},
      {id:'home.product-banners.title',label:'عنوان بنرها',kind:'title',textEditable:true},
      {id:'home.product-banners.intro',label:'مقدمه بنرها',kind:'text',textEditable:true},
      ...bannerTargets,
    ],
  },
  {
    id:'products',
    label:'محصولات',
    targets:[
      {id:'products.page',label:'زمینه صفحه محصولات',kind:'section',hideable:false,styleControls:['backgroundColor']},
      {id:'product.card',label:'قاب همه کارت‌های محصول',kind:'panel'},
      {id:'product.media',label:'بخش تصویر کارت',kind:'media'},
      {id:'product.title',label:'عنوان کارت محصول',kind:'title'},
      {id:'product.code',label:'کد محصول',kind:'text'},
      {id:'product.actions',label:'دکمه‌های کارت محصول',kind:'control'},
    ],
  },
  {
    id:'footer',
    label:'فوتر',
    targets:[
      {id:'footer.shell',label:'کل فوتر',kind:'section'},
      {id:'footer.brand',label:'لوگوی فوتر',kind:'panel'},
      {id:'footer.columns',label:'ستون‌های فوتر',kind:'panel'},
    ],
  },
]

export const visualTargetById=new Map(
  visualTargetGroups.flatMap(group=>group.targets.map(target=>[target.id,target] as const)),
)

export function visualTargetDefinition(id:string):VisualTargetDefinition|undefined{
  return visualTargetById.get(id)
}
