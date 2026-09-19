const cats={
 '1':{name:{fa:'نوزادی',ar:'حديثو الولادة',en:'Baby',ku:'ساوا'},subs:['11','12']},
 '2':{name:{fa:'بچگانه',ar:'أطفال',en:'Kids',ku:'منداڵان'},subs:['21','22']},
 '3':{name:{fa:'زنانه',ar:'نسائي',en:'Women',ku:'ژنان'},subs:['31','32']}
};
const subs={
 '11':{parent:'1',name:{fa:'لباس نوزادی',ar:'ملابس حديثي الولادة',en:'Baby clothing',ku:'جل و بەرگی ساوا'},flex:['appearance','modelSpec','items','size','age'],locked:['color','material']},
 '12':{parent:'1',name:{fa:'پتوی نوزادی',ar:'بطانية أطفال',en:'Baby blanket',ku:'پەتووی ساوا'},flex:['appearance','form','modelSpec','colorMix','material','packQty'],locked:['dimensions']},
 '21':{parent:'2',name:{fa:'دخترانه',ar:'بناتي',en:'Girls',ku:'کچان'},flex:['appearance','components','form','modelSpec','fit','size','age','color','material','packQty'],locked:[]},
 '22':{parent:'2',name:{fa:'پسرانه',ar:'ولادي',en:'Boys',ku:'کوڕان'},flex:['appearance','components','form','modelSpec','fit','size','age','color','material','packQty'],locked:[]},
 '31':{parent:'3',name:{fa:'زیرسارافون (تونیک)',ar:'تونيك',en:'Tunic',ku:'تونیک'},flex:['appearance','form'],locked:['size','color','material','packQty']},
 '32':{parent:'3',name:{fa:'لباس راحتی (ورزشی)',ar:'ملابس مريحة (رياضية)',en:'Casual / sportswear',ku:'جلی ئاسوودە (وەرزشی)'},flex:['appearance','components','form','productSpec','fit','material','color'],locked:['size','packQty']}
};
const specNames={
 appearance:{fa:'طرح ظاهری',ar:'التصميم الخارجي',en:'Visual design',ku:'دیزاینی دەرەوە'},components:{fa:'اجزای محصول',ar:'مكونات المنتج',en:'Product components',ku:'پێکهاتەکانی بەرهەم'},form:{fa:'فرم محصول',ar:'شكل المنتج',en:'Product form',ku:'فۆرمی بەرهەم'},modelSpec:{fa:'مشخصات مدل',ar:'مواصفات الموديل',en:'Model specifications',ku:'تایبەتمەندی مۆدێل'},productSpec:{fa:'مشخصات محصول',ar:'مواصفات المنتج',en:'Product specifications',ku:'تایبەتمەندی بەرهەم'},fit:{fa:'نوع تن‌خور',ar:'نوع القياس/القصّة',en:'Fit',ku:'جۆری لەبەرکردن'},material:{fa:'جنس',ar:'الخامة',en:'Material',ku:'ماددە'},size:{fa:'سایز',ar:'المقاس',en:'Size',ku:'قەبارە'},color:{fa:'رنگ',ar:'اللون',en:'Color',ku:'ڕەنگ'},packQty:{fa:'تعداد در پک',ar:'العدد في العبوة',en:'Pack quantity',ku:'ژمارە لە پاکەت'},age:{fa:'رده سنی',ar:'الفئة العمرية',en:'Age range',ku:'تەمەن'},items:{fa:'اقلام محصول',ar:'عناصر المنتج',en:'Included items',ku:'بڕگەکانی بەرهەم'},typeMix:{fa:'نوع ترکیب',ar:'نوع التركيب',en:'Combination type',ku:'جۆری تێکەڵ'},colorMix:{fa:'ترکیب رنگ',ar:'تركيبة الألوان',en:'Color combination',ku:'تێکەڵی ڕەنگ'},length:{fa:'قد محصول',ar:'طول المنتج',en:'Product length',ku:'درێژی بەرهەم'},use:{fa:'نوع کاربرد',ar:'نوع الاستخدام',en:'Use case',ku:'جۆری بەکارهێنان'},dimensions:{fa:'ابعاد',ar:'الأبعاد',en:'Dimensions',ku:'ڕەهەندەکان'}
};
const products=[];
Object.keys(subs).forEach((s,si)=>{for(let n=1;n<=2;n++){products.push({code:`${s}${String(n).padStart(3,'0')}`,sub:s,status:(si+n)%3===0?'out':'in',variant:n});}});
let lang=localStorage.getItem('armaghan-lang')||'fa'; if(!tx[lang])lang='fa';
let category='1', sub='11', status='all', query='';
let favs=new Set(JSON.parse(localStorage.getItem('armaghan-favorites-v2')||'[]'));
let orderState={step:0,path:null,cat:null,sub:null,note:''};
let installPrompt=null;
function t(k){return tx[lang][k]||tx.fa[k]||k}function nCat(id){return cats[id].name[lang]||cats[id].name.fa}function nSub(id){return subs[id].name[lang]||subs[id].name.fa}function nSpec(k){return specNames[k]?.[lang]||specNames[k]?.fa||k}
function setLang(l){lang=l;localStorage.setItem('armaghan-lang',l);document.documentElement.lang=l;document.documentElement.dir=tx[l].dir;layout();}
function garmentSvg(subId){const type=subId==='12'?'blanket':subId==='31'?'tunic':'shirt';if(type==='blanket')return '<svg class="garment" viewBox="0 0 120 100" fill="none" stroke="currentColor" stroke-width="4"><rect x="18" y="18" width="84" height="64" rx="9"/><path d="M31 31h58M31 45h38M31 59h48"/></svg>';if(type==='tunic')return '<svg class="garment" viewBox="0 0 120 120" fill="none" stroke="currentColor" stroke-width="4"><path d="M43 18c5 8 29 8 34 0l14 13-12 17-5-5 8 59H38l8-59-5 5-12-17z"/><path d="M50 20c2 10 18 10 20 0"/></svg>';return '<svg class="garment" viewBox="0 0 120 110" fill="none" stroke="currentColor" stroke-width="4"><path d="M43 18c5 8 29 8 34 0l24 17-13 20-12-8v47H44V47l-12 8-13-20z"/><path d="M50 20c2 10 18 10 20 0"/></svg>';}
function productName(p){return `${nSub(p.sub)} — ${t('model')} ${p.variant===1?'A':'B'}`}
