export type OrderMove='first'|'up'|'down'|'last'
export function moveOrderedProduct<T extends {id:number}>(rows:T[],id:number,move:OrderMove):T[]{
 const next=[...rows],index=next.findIndex(row=>row.id===id)
 if(index<0)return next
 const target=move==='first'?0:move==='last'?next.length-1:move==='up'?Math.max(0,index-1):Math.min(next.length-1,index+1)
 const [row]=next.splice(index,1);next.splice(target,0,row!);return next
}
export const sameProductOrder=(a:{id:number}[],b:{id:number}[])=>a.length===b.length&&a.every((row,index)=>row.id===b[index]?.id)
