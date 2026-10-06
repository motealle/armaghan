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
    image:'./images/test29/home/hero-customer-trade.webp',
    fallback:'./images/test29/home/hero-customer-trade.webp',
  },
  about:{
    image:'./images/test26/home/about-armaghan.webp',
    fallback:'./images/test26/home/about-armaghan.webp',
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
    fallback:'./images/test26/home/capability-production.webp',
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
    fallback:'./images/test26/home/capability-export-prep.webp',
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
    fallback:'./images/test26/home/capability-documents.webp',
  },
]

export const productBannerMedia:Record<string,{image:string;fallback:string}>={
  '1':{
    image:'./images/test26/home/banner-baby.webp',
    fallback:'./images/test26/home/banner-baby.webp',
  },
  '2':{
    image:'./images/test26/home/banner-kids.webp',
    fallback:'./images/test26/home/banner-kids.webp',
  },
  '3':{
    image:'./images/test26/home/banner-women.webp',
    fallback:'./images/test26/home/banner-women.webp',
  },
}
