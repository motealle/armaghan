export const homeImageTargets = [
  { id:'hero.media', target:'hero', labels:{fa:'تصویر اصلی',en:'Hero',ar:'الصورة الرئيسية',ku:'وێنەی سەرەکی'} },
  { id:'home.about.media', target:'about', labels:{fa:'درباره',en:'About',ar:'عن الشركة',ku:'دەربارە'} },
  { id:'home.capability.production.media', target:'capability.production', labels:{fa:'تولید',en:'Production',ar:'الإنتاج',ku:'بەرهەمهێنان'} },
  { id:'home.capability.export.media', target:'capability.export', labels:{fa:'صادرات',en:'Export',ar:'التصدير',ku:'هەناردە'} },
  { id:'home.capability.trade.media', target:'capability.trade', labels:{fa:'تجارت',en:'Trade',ar:'التجارة',ku:'بازرگانی'} },
  { id:'home.product-banner.1.media', target:'banner.1', labels:{fa:'نوزادی',en:'Baby',ar:'الرضع',ku:'ساوا'} },
  { id:'home.product-banner.2.media', target:'banner.2', labels:{fa:'بچگانه',en:'Kids',ar:'الأطفال',ku:'منداڵان'} },
  { id:'home.product-banner.3.media', target:'banner.3', labels:{fa:'زنانه',en:'Women',ar:'النساء',ku:'ژنان'} },
] as const

export function homeImageTarget(id:string) {
  return homeImageTargets.find(item=>{
    const panel=item.id.replace(/\.media$/,'')
    return id===panel||id.startsWith(panel+'.')
  })?.target??''
}
