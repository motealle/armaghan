const aliases:Record<string,string>={
  "whyArmaghanEyebrow": "home.why.eyebrow",
  "whyArmaghanTitle": "home.why.title",
  "whyArmaghanIntro": "home.why.intro",
  "productBannersEyebrow": "home.product-banners.eyebrow",
  "productBannersTitle": "home.product-banners.title",
  "productBannersIntro": "home.product-banners.intro",
  "heroSingleSlogan": "hero.title",
  "aboutArmaghanEyebrow": "home.about.eyebrow",
  "aboutArmaghanTitle": "home.about.title",
  "aboutArmaghanText": "home.about.text",
  "capabilitiesEyebrow": "home.capabilities.eyebrow",
  "capabilitiesTitle": "home.capabilities.title",
  "capabilitiesIntro": "home.capabilities.intro",
  "brandName": "header.brand-name",
  "manufacturer": "header.manufacturer",
  "whyCapacityTitle": "home.why.item.1.title",
  "whyCapacityText": "home.why.item.1.text",
  "whyCustomTitle": "home.why.item.2.title",
  "whyCustomText": "home.why.item.2.text",
  "whyDirectTitle": "home.why.item.3.title",
  "whyDirectText": "home.why.item.3.text",
  "whyMarketTitle": "home.why.item.4.title",
  "whyMarketText": "home.why.item.4.text",
  "brandCapabilities": "home.capability.production.title",
  "brandCapabilitiesText": "home.capability.production.text",
  "brandDocuments": "home.capability.export.title",
  "brandDocumentsText": "home.capability.export.text",
  "brandSales": "home.capability.trade.title",
  "brandSalesText": "home.capability.trade.text"
}

export function translationTarget(key:string):string{
  return aliases[key]??('copy.'+Array.from(new TextEncoder().encode(key)).map(byte=>byte.toString(16).padStart(2,'0')).join(''))
}
