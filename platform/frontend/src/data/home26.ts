export interface CapabilityContent{
  id:'production'|'export'|'trade'
  titleKey:string
  summaryKey:string
  detailKeys:string[]
  image:string
  fallback:string
}

export const test26Media={
  hero:{
    image:'./images/final/hero/hero-brand.webp',
    fallback:'https://placehold.co/1920x1080/0B2340/FFFFFF.webp?text=IMAGE+REQUIRED%0AHome+Hero+1920x1080',
  },
  about:{
    image:'./images/test26/home/about-armaghan.webp',
    fallback:'https://placehold.co/1600x1200/E8F2EF/10243E.webp?text=IMAGE+REQUIRED%0AAbout+Armaghan+1600x1200',
  },
} as const

export const capabilities:CapabilityContent[]=[
  {
    id:'production',
    titleKey:'capabilityProductionTitle',
    summaryKey:'capabilityProductionSummary',
    detailKeys:[
      'capabilityProductionDetail1','capabilityProductionDetail2','capabilityProductionDetail3',
      'capabilityProductionDetail4','capabilityProductionDetail5','capabilityProductionDetail6','capabilityProductionDetail7',
    ],
    image:'./images/test26/home/capability-production.webp',
    fallback:'https://placehold.co/1200x675/E8F2EF/10243E.webp?text=IMAGE+REQUIRED%0AProduction+Capability',
  },
  {
    id:'export',
    titleKey:'capabilityExportTitle',
    summaryKey:'capabilityExportSummary',
    detailKeys:[
      'capabilityExportDetail1','capabilityExportDetail2','capabilityExportDetail3','capabilityExportDetail4',
      'capabilityExportDetail5','capabilityExportDetail6','capabilityExportDetail7','capabilityExportDetail8',
    ],
    image:'./images/test26/home/capability-export-prep.webp',
    fallback:'https://placehold.co/1200x675/E8F2EF/10243E.webp?text=IMAGE+REQUIRED%0AExport+Preparation',
  },
  {
    id:'trade',
    titleKey:'capabilityTradeTitle',
    summaryKey:'capabilityTradeSummary',
    detailKeys:[
      'capabilityTradeDetail1','capabilityTradeDetail2','capabilityTradeDetail3','capabilityTradeDetail4',
      'capabilityTradeDetail5','capabilityTradeDetail6','capabilityTradeDetail7','capabilityTradeDetail8',
    ],
    image:'./images/test26/home/capability-documents.webp',
    fallback:'https://placehold.co/1200x675/E8F2EF/10243E.webp?text=IMAGE+REQUIRED%0ADocuments+and+Trade',
  },
]

export const productBannerMedia:Record<string,{image:string;fallback:string}>={
  '1':{
    image:'./images/test26/home/banner-baby.webp',
    fallback:'https://placehold.co/1600x600/F1F5F4/10243E.webp?text=IMAGE+REQUIRED%0ABaby+Category+Banner',
  },
  '2':{
    image:'./images/test26/home/banner-kids.webp',
    fallback:'https://placehold.co/1600x600/F1F5F4/10243E.webp?text=IMAGE+REQUIRED%0AKids+Category+Banner',
  },
  '3':{
    image:'./images/test26/home/banner-women.webp',
    fallback:'https://placehold.co/1600x600/F1F5F4/10243E.webp?text=IMAGE+REQUIRED%0AWomen+Category+Banner',
  },
}
