export type ViewportProfile='mobile'|'tablet'|'desktop'
export type HeaderMode='compact-drawer'|'expanded'
export type HeroMode='single'|'carousel'
export type HomeProductGridMode='hidden'|'recommended-6'

export interface ViewportAppearance{
  headerMode:HeaderMode
  showHamburger:boolean
  showBrandText:boolean
  showLanguage:boolean
  showHelp:boolean
  showAccount:boolean
  heroMode:HeroMode
  homeProductGrid:HomeProductGridMode
  showCategoryNumbers:boolean
  showSubcategoryCodes:boolean
  showAbout:boolean
  showWhy:boolean
  showCapabilities:boolean
  showProductBanners:boolean
  showFooter:boolean
}

export type AppearanceProfiles=Record<ViewportProfile,ViewportAppearance>

export const viewportProfiles:ViewportProfile[]=['mobile','tablet','desktop']
