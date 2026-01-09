const __vite__mapDeps=(i,m=__vite__mapDeps,d=(m.f||(m.f=["assets/PaymentReceipt-D1a_tXiR.js","assets/app-CmJRaKAb.js","assets/app-DfUGuj8f.css","assets/_plugin-vue_export-helper-DlAUqK2U.js","assets/PaymentReceipt-D6f2BIQq.css"])))=>i.map(i=>d[i]);
import{r as b,c as U,x as I,a as p,o as m,b as e,g as B,F as L,e as j,h as v,t as i,i as g,B as R,p as fe,k as q,n as T,y as Ce,z as _e,d as N,w as he,f as Ue,j as S,A as Ve,u as Me,_ as qe}from"./app-CmJRaKAb.js";import{P as Fe}from"./PaymentReceipt-D1a_tXiR.js";import{_ as be}from"./_plugin-vue_export-helper-DlAUqK2U.js";const Pe={class:"payment-method-form"},Te={class:"form-header"},De=["disabled"],ze={class:"methods-list"},Ee={class:"method-header"},Re={class:"method-number"},Ae=["onClick"],Ie={class:"method-fields"},Ne={class:"form-group"},Se=["for"],Be=["id","onUpdate:modelValue","onChange"],Le={class:"form-group"},je=["for"],Qe=["id","onUpdate:modelValue"],Ze={key:0,class:"error-message"},He={key:0,class:"form-group"},Ke=["for"],We=["id","onUpdate:modelValue"],Oe={key:1,class:"form-group"},Ge=["for"],Je=["id","onUpdate:modelValue","placeholder"],Xe={class:"summary-row"},Ye={class:"summary-value"},et={class:"summary-row"},tt={class:"summary-value"},at={key:0,class:"summary-row error-row"},st={class:"summary-value"},ot={__name:"PaymentMethodForm",props:{requiredTotal:{type:Number,required:!0},initialMethods:{type:Array,default:()=>[]}},emits:["update:methods","validation-change"],setup(D,{emit:A}){const a=D,k=A;let c=0;const u=b([]);a.initialMethods.length>0?u.value=a.initialMethods.map(r=>({id:c++,method:r.method||"",amount:r.amount||0,payment_system:r.payment_system||"",reference:r.reference||"",amountError:""})):u.value=[{id:c++,method:"cash",amount:a.requiredTotal,payment_system:"",reference:"",amountError:""}];const y=U(()=>u.value.reduce((r,l)=>r+(parseFloat(l.amount)||0),0)),f=U(()=>Math.abs(y.value-a.requiredTotal)>.01),V=U(()=>u.value.length<5),o=U(()=>u.value.every(l=>l.method&&l.amount>0)&&!f.value);function z(){if(!V.value)return;const r=a.requiredTotal-y.value;u.value.push({id:c++,method:"",amount:r>0?r:0,payment_system:"",reference:"",amountError:""})}function $(r){u.value.length<=1||(u.value.splice(r,1),w())}function M(r){const l=u.value[r];l.method!=="card"&&(l.payment_system=""),["card","transfer","p2p"].includes(l.method)||(l.reference="")}function w(){u.value.forEach(r=>{r.amountError=""}),f.value&&(y.value-a.requiredTotal>0?u.value[u.value.length-1].amountError="Общая сумма превышает требуемую":u.value[u.value.length-1].amountError="Общая сумма меньше требуемой")}function F(r){return new Intl.NumberFormat("ru-RU",{minimumFractionDigits:2,maximumFractionDigits:2}).format(r||0)}return I(u,()=>{const r=u.value.map(l=>({method:l.method,amount:parseFloat(l.amount)||0,payment_system:l.payment_system||null,reference:l.reference||null}));k("update:methods",r)},{deep:!0}),I(o,r=>{k("validation-change",r)}),I(()=>a.requiredTotal,r=>{u.value.length===1&&(u.value[0].amount=r),w()}),w(),k("validation-change",o.value),(r,l)=>(m(),p("div",Pe,[e("div",Te,[l[1]||(l[1]=e("label",{class:"section-label"},"Методы оплаты *",-1)),e("button",{type:"button",onClick:z,class:"btn-add-method",disabled:!V.value},[...l[0]||(l[0]=[e("svg",{xmlns:"http://www.w3.org/2000/svg",width:"16",height:"16",viewBox:"0 0 24 24",fill:"none",stroke:"currentColor","stroke-width":"2"},[e("line",{x1:"12",y1:"5",x2:"12",y2:"19"}),e("line",{x1:"5",y1:"12",x2:"19",y2:"12"})],-1),B(" Добавить метод ",-1)])],8,De)]),e("div",ze,[(m(!0),p(L,null,j(u.value,(d,P)=>(m(),p("div",{key:d.id,class:"method-item"},[e("div",Ee,[e("span",Re,"Метод #"+i(P+1),1),u.value.length>1?(m(),p("button",{key:0,type:"button",onClick:_=>$(P),class:"btn-remove",title:"Удалить метод"},[...l[2]||(l[2]=[e("svg",{xmlns:"http://www.w3.org/2000/svg",width:"16",height:"16",viewBox:"0 0 24 24",fill:"none",stroke:"currentColor","stroke-width":"2"},[e("line",{x1:"18",y1:"6",x2:"6",y2:"18"}),e("line",{x1:"6",y1:"6",x2:"18",y2:"18"})],-1)])],8,Ae)):v("",!0)]),e("div",Ie,[e("div",Ne,[e("label",{for:`method-type-${d.id}`},"Способ оплаты *",8,Se),g(e("select",{id:`method-type-${d.id}`,"onUpdate:modelValue":_=>d.method=_,class:"form-select",required:"",onChange:_=>M(P)},[...l[3]||(l[3]=[fe('<option value="" data-v-86e090e3>Выберите способ</option><option value="cash" data-v-86e090e3>Наличные</option><option value="card" data-v-86e090e3>Карта</option><option value="transfer" data-v-86e090e3>Безналичный перевод</option><option value="p2p" data-v-86e090e3>P2P перевод</option>',5)])],40,Be),[[R,d.method]])]),e("div",Le,[e("label",{for:`method-amount-${d.id}`},"Сумма *",8,je),g(e("input",{id:`method-amount-${d.id}`,"onUpdate:modelValue":_=>d.amount=_,type:"number",step:"0.01",min:"0",class:T(["form-input",{error:d.amountError}]),placeholder:"0.00",required:"",onInput:w},null,42,Qe),[[q,d.amount,void 0,{number:!0}]]),d.amountError?(m(),p("span",Ze,i(d.amountError),1)):v("",!0)]),d.method==="card"?(m(),p("div",He,[e("label",{for:`method-system-${d.id}`},"Платежная система",8,Ke),g(e("select",{id:`method-system-${d.id}`,"onUpdate:modelValue":_=>d.payment_system=_,class:"form-select"},[...l[4]||(l[4]=[fe('<option value="" data-v-86e090e3>Не указано</option><option value="uzcard" data-v-86e090e3>UzCard</option><option value="humo" data-v-86e090e3>Humo</option><option value="visa" data-v-86e090e3>Visa</option><option value="mastercard" data-v-86e090e3>MasterCard</option><option value="unionpay" data-v-86e090e3>UnionPay</option>',6)])],8,We),[[R,d.payment_system]])])):v("",!0),d.method==="transfer"||d.method==="p2p"||d.method==="card"?(m(),p("div",Oe,[e("label",{for:`method-reference-${d.id}`},i(d.method==="card"?"Последние 4 цифры карты":d.method==="transfer"?"Номер транзакции":"Номер P2P операции"),9,Ge),g(e("input",{id:`method-reference-${d.id}`,"onUpdate:modelValue":_=>d.reference=_,type:"text",class:"form-input",placeholder:d.method==="card"?"1234":"Необязательно"},null,8,Je),[[q,d.reference]])])):v("",!0)])]))),128))]),e("div",{class:T(["total-summary",{error:f.value}])},[e("div",Xe,[l[5]||(l[5]=e("span",{class:"summary-label"},"Всего по методам:",-1)),e("span",Ye,i(F(y.value)),1)]),e("div",et,[l[6]||(l[6]=e("span",{class:"summary-label"},"Требуемая сумма:",-1)),e("span",tt,i(F(D.requiredTotal)),1)]),f.value?(m(),p("div",at,[l[7]||(l[7]=e("span",{class:"summary-label"},"Разница:",-1)),e("span",st,i(F(Math.abs(y.value-D.requiredTotal))),1)])):v("",!0)],2)]))}},lt=be(ot,[["__scopeId","data-v-86e090e3"]]),nt={class:"create-payment-page"},it={class:"page-header"},rt={class:"form-card"},dt={class:"form-row"},ut={class:"form-group"},mt=["value"],pt={key:0,class:"error-message"},ct={class:"form-group"},vt={class:"form-row"},yt={class:"form-group"},ft={key:0,class:"error-message"},_t={class:"form-group"},ht={key:0,class:"error-message"},bt={class:"form-row"},gt={class:"form-group"},wt={class:"form-group"},$t={key:0,class:"commission-info"},xt={class:"info-row"},kt={class:"info-value"},Ct={class:"info-row"},Ut={class:"info-value"},Vt={class:"info-row total"},Mt={class:"info-value"},qt={key:2,class:"alert alert-error"},Ft={class:"form-actions"},Pt=["disabled"],Tt={class:"modal-body"},Dt={class:"confirm-details"},zt={class:"detail-row"},Et={class:"detail-value"},Rt={class:"detail-row"},At={class:"detail-value"},It={class:"detail-row"},Nt={class:"detail-value"},St={class:"detail-row"},Bt={class:"detail-value"},Lt={class:"detail-row total"},jt={class:"detail-value"},Qt={key:0,class:"methods-breakdown"},Zt={class:"method-label"},Ht={class:"method-value"},Kt={key:0,class:"method-extra"},Wt={class:"modal-footer"},Ot=["disabled"],Gt=["disabled"],Jt={class:"modal-body"},Xt={__name:"Create",setup(D){const A=Me(),a=b({payment_type_id:"",payer_name:"",purpose:"",amount:0,currency:"UZS",list_number:"",method_details:[]}),k=b([]),c=b({}),u=b(null),y=b(!1),f=b(!1),V=b(!1),o=b(null),z=b(!1),$=U(()=>k.value.find(s=>s.id===a.value.payment_type_id)),M=U(()=>{if(!$.value||!a.value.amount)return 0;let s=0;return $.value.commission_percentage&&(s+=a.value.amount*$.value.commission_percentage/100),$.value.commission_fixed&&(s+=parseFloat($.value.commission_fixed)),Math.round(s*100)/100}),w=U(()=>a.value.amount+M.value);function F(s){a.value.method_details=s}function r(s){z.value=s}function l(s){return{cash:"Наличные",card:"Карта",transfer:"Безналичный перевод",p2p:"P2P перевод"}[s]||s}Ce(async()=>{await d()});async function d(){try{const s=await _e.get("/payment-types");s.data.success&&(k.value=s.data.data)}catch(s){console.error("Error loading payment types:",s)}}async function P(){c.value={},u.value=null,f.value=!0}async function _(){var s,t,C,x;y.value=!0,c.value={},u.value=null;try{const n=await _e.post("/payments",a.value);n.data.success&&(o.value=n.data.data,f.value=!1,V.value=!0)}catch(n){(t=(s=n.response)==null?void 0:s.data)!=null&&t.errors&&(c.value=n.response.data.errors),u.value=((x=(C=n.response)==null?void 0:C.data)==null?void 0:x.message)||"Ошибка при создании платежа",f.value=!1}finally{y.value=!1}}function Q(){V.value=!1,A.push({name:"Payments"})}async function ge(){var x,n,E,Z,H,K,W,O,G,J,X,Y,ee,te,ae,se,oe,le,ne,ie,re,de,ue,me,pe,ce,ve;let s="";if((x=o.value)!=null&&x.share_url)try{s=await(await qe(async()=>{const{default:xe}=await import("./PaymentReceipt-D1a_tXiR.js").then(ke=>ke.b);return{default:xe}},__vite__mapDeps([0,1,2,3,4]))).default.toDataURL(o.value.share_url,{width:60,margin:1,color:{dark:"#000000",light:"#FFFFFF"}})}catch(ye){console.error("QR generation failed:",ye)}const t=window.open("","_blank"),C=`
    <html>
      <head>
        <title>Квитанция - ${(n=o.value)==null?void 0:n.payment_number}</title>
        <style>
          @page {
            size: A4;
            margin: 0;
          }

          * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
          }

          html,
          body {
            width: 210mm;
            height: 297mm;
            margin: 0;
            padding: 0;
          }

          body {
            font-family: 'Verdana', Arial, sans-serif;
            padding: 5mm;
          }

          .receipt-wrapper {
            width: 190mm;
            margin: 0 auto;
            padding: 0;
          }

          .receipt-container {
            width: 190mm;
            height: 75mm;
            border: 1px solid #000;
            display: flex;
            background: white;
            font-family: 'Verdana', Arial, sans-serif;
            overflow: hidden;
          }

          .receipt-left {
            width: 50mm;
            border-right: 1px solid #000;
            padding: 2mm;
            text-align: center;
            display: flex;
            flex-direction: column;
            gap: 1mm;
          }

          .receipt-label {
            font-size: 8pt;
            font-weight: bold;
            margin: 0;
            padding: 0.5mm 0;
            line-height: 1.2;
          }

          .qr-code {
            width: 16mm;
            height: 16mm;
            margin: 1mm auto;
            display: flex;
            align-items: center;
            justify-content: center;
          }

          .qr-code img {
            width: 16mm;
            height: 16mm;
            display: block;
          }

          .signature-field {
            margin-top: auto;
            padding-top: 1mm;
            text-align: left;
          }

          .signature-label {
            font-size: 7pt;
            font-weight: bold;
            margin-bottom: 0.5mm;
          }

          .signature-line {
            border-top: 1px solid #000;
            width: 100%;
            margin-top: 2mm;
          }

          .receipt-right {
            flex: 1;
            display: flex;
            flex-direction: column;
          }

          .receipt-header {
            text-align: center;
            border-bottom: 1px solid #000;
            font-size: 8pt;
            padding: 1mm;
            font-weight: bold;
          }

          .receipt-details {
            padding: 2mm;
          }

          .detail-line {
            display: flex;
            font-size: 8pt;
            margin-bottom: 0.5mm;
            align-items: baseline;
          }

          .detail-label {
            font-weight: bold;
            min-width: 25mm;
            margin-right: 2mm;
          }

          .detail-value {
            flex: 1;
            border-bottom: 1px solid #000;
            padding-bottom: 0.5mm;
          }

          .payer-name {
            text-align: center;
            font-size: 11pt;
            font-weight: bold;
            margin: 2mm;
          }

          .purpose-section {
            text-align: center;
            margin: 0 2mm;
            border-top: 1px solid #000;
            padding-top: 1mm;
          }

          .purpose-text {
            font-size: 9pt;
            margin: 1mm 0;
            font-weight: 600;
          }

          .purpose-label {
            font-size: 7pt;
            color: #666;
            border-top: 1px solid #000;
            padding-top: 0.5mm;
          }

          .amounts-section {
            margin-top: auto;
            border-top: 1px solid #000;
          }

          .amount-row {
            display: flex;
            border-bottom: 1px solid #000;
          }

          .amount-label {
            font-size: 8pt;
            padding: 1.5mm 2mm;
            flex: 1;
          }

          .amount-value {
            font-size: 9pt;
            padding: 1.5mm 2mm;
            text-align: right;
            min-width: 60mm;
            white-space: nowrap;
            border-left: 1px solid #000;
          }

          .total-row {
            border-bottom: none;
          }

          .total-row .amount-label,
          .total-row .amount-value {
            font-weight: bold;
            font-size: 9pt;
          }

          @media print {
            * {
              -webkit-print-color-adjust: exact;
              print-color-adjust: exact;
            }

            body {
              margin: 0 !important;
              padding: 5mm !important;
            }

            .receipt-wrapper {
              width: 190mm !important;
              margin: 0 auto !important;
            }

            .receipt-container {
              width: 190mm !important;
              height: 75mm !important;
              page-break-inside: avoid;
              border: 1px solid #000 !important;
            }

            .receipt-left {
              width: 50mm !important;
              border-right: 1px solid #000 !important;
            }

            .amount-row {
              border-bottom: 1px solid #000 !important;
            }

            .amount-value {
              border-left: 1px solid #000 !important;
            }

            .detail-value {
              border-bottom: 1px solid #000 !important;
            }
          }
        </style>
      </head>
      <body>
        <div class="receipt-wrapper">
          <div class="receipt-container">
            <div class="receipt-left">
              <h4 class="receipt-label">Хабарнома</h4>
              <h4 class="receipt-label">Paynes Kassa</h4>
              <h4 class="receipt-label">${((E=o.value)==null?void 0:E.payment_method)==="cash"?"Наличный":"Безналичный"}</h4>
              <h4 class="receipt-label">${we((Z=o.value)==null?void 0:Z.created_at)}</h4>
              <h4 class="receipt-label">${$e((H=o.value)==null?void 0:H.created_at)}</h4>
              ${s?`
                <div class="qr-code">
                  <img src="${s}" alt="QR Code" />
                </div>
              `:""}
              <h4 class="receipt-label">№ ${((K=o.value)==null?void 0:K.payment_number)||""}</h4>
              <h4 class="receipt-label">${((O=(W=o.value)==null?void 0:W.cashier)==null?void 0:O.full_name)||""}</h4>

              <div class="signature-field">
                <div class="signature-label">Подпись:</div>
                <div class="signature-line"></div>
              </div>
            </div>

            <div class="receipt-right">
              <h4 class="receipt-header">${((J=(G=o.value)==null?void 0:G.payment_type)==null?void 0:J.name)||""}</h4>

              <div class="receipt-details">
                ${(Y=(X=o.value)==null?void 0:X.payment_type)!=null&&Y.bank_name?`
                  <div class="detail-line">
                    <span class="detail-label">Банк:</span>
                    <span class="detail-value">${o.value.payment_type.bank_name}</span>
                  </div>
                `:""}
                ${(te=(ee=o.value)==null?void 0:ee.payment_type)!=null&&te.account_number?`
                  <div class="detail-line">
                    <span class="detail-label">Счет №:</span>
                    <span class="detail-value">${o.value.payment_type.account_number}</span>
                  </div>
                `:""}
                ${(se=(ae=o.value)==null?void 0:ae.payment_type)!=null&&se.mfo?`
                  <div class="detail-line">
                    <span class="detail-label">МФО:</span>
                    <span class="detail-value">${o.value.payment_type.mfo}</span>
                  </div>
                `:""}
                ${(le=(oe=o.value)==null?void 0:oe.payment_type)!=null&&le.inn?`
                  <div class="detail-line">
                    <span class="detail-label">ИНН:</span>
                    <span class="detail-value">${o.value.payment_type.inn}</span>
                  </div>
                `:""}
                ${(ne=o.value)!=null&&ne.list_number?`
                  <div class="detail-line">
                    <span class="detail-label">Лицевой счет:</span>
                    <span class="detail-value">${o.value.list_number}</span>
                  </div>
                `:""}
              </div>

              <h4 class="payer-name">${((ie=o.value)==null?void 0:ie.payer_name)||""}</h4>

              ${(re=o.value)!=null&&re.purpose?`
                <div class="purpose-section">
                  <p class="purpose-text">${o.value.purpose}</p>
                  <p class="purpose-label">(Назначение платежа / Адрес)</p>
                </div>
              `:""}

              <div class="amounts-section">
                <div class="amount-row">
                  <span class="amount-label">Сумма:</span>
                  <span class="amount-value">${h((de=o.value)==null?void 0:de.amount)} ${((ue=o.value)==null?void 0:ue.currency)||""}</span>
                </div>
                <div class="amount-row">
                  <span class="amount-label">Комиссия:</span>
                  <span class="amount-value">${h((me=o.value)==null?void 0:me.commission_amount)} ${((pe=o.value)==null?void 0:pe.currency)||""}</span>
                </div>
                <div class="amount-row total-row">
                  <span class="amount-label">Итого:</span>
                  <span class="amount-value">${h((ce=o.value)==null?void 0:ce.total_amount)} ${((ve=o.value)==null?void 0:ve.currency)||""}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </body>
    </html>
  `;t.document.write(C),t.document.close(),t.focus(),setTimeout(()=>{t.print(),t.close()},250)}function we(s){if(!s)return"";const t=new Date(s);return new Intl.DateTimeFormat("ru-RU",{year:"numeric",month:"2-digit",day:"2-digit"}).format(t)}function $e(s){if(!s)return"";const t=new Date(s);return new Intl.DateTimeFormat("ru-RU",{hour:"2-digit",minute:"2-digit"}).format(t)}function h(s){return s?new Intl.NumberFormat("ru-RU",{minimumFractionDigits:2,maximumFractionDigits:2}).format(s):"0.00"}return(s,t)=>{var x;const C=Ue("router-link");return m(),p("div",nt,[e("div",it,[t[11]||(t[11]=e("h1",{class:"page-title"},"Новый платеж",-1)),N(C,{to:"/payments",class:"btn btn-secondary"},{default:he(()=>[...t[10]||(t[10]=[B(" ← Назад ",-1)])]),_:1})]),e("div",rt,[e("form",{onSubmit:S(P,["prevent"])},[e("div",dt,[e("div",ut,[t[13]||(t[13]=e("label",{for:"payment_type_id"},"Тип платежа *",-1)),g(e("select",{id:"payment_type_id","onUpdate:modelValue":t[0]||(t[0]=n=>a.value.payment_type_id=n),class:T(["form-select",{error:c.value.payment_type_id}]),required:""},[t[12]||(t[12]=e("option",{value:""},"Выберите тип платежа",-1)),(m(!0),p(L,null,j(k.value,n=>(m(),p("option",{key:n.id,value:n.id},i(n.name)+" ("+i(n.commission_percentage)+"%) ",9,mt))),128))],2),[[R,a.value.payment_type_id]]),c.value.payment_type_id?(m(),p("span",pt,i(c.value.payment_type_id[0]),1)):v("",!0)]),e("div",ct,[t[14]||(t[14]=e("label",{for:"list_number"},"Номер списка",-1)),g(e("input",{id:"list_number","onUpdate:modelValue":t[1]||(t[1]=n=>a.value.list_number=n),type:"text",class:"form-input",placeholder:"Номер списка"},null,512),[[q,a.value.list_number]])])]),e("div",vt,[e("div",yt,[t[15]||(t[15]=e("label",{for:"payer_name"},"Плательщик *",-1)),g(e("input",{id:"payer_name","onUpdate:modelValue":t[2]||(t[2]=n=>a.value.payer_name=n),type:"text",class:T(["form-input",{error:c.value.payer_name}]),placeholder:"ФИО плательщика",required:""},null,2),[[q,a.value.payer_name]]),c.value.payer_name?(m(),p("span",ft,i(c.value.payer_name[0]),1)):v("",!0)]),e("div",_t,[t[16]||(t[16]=e("label",{for:"amount"},"Сумма *",-1)),g(e("input",{id:"amount","onUpdate:modelValue":t[3]||(t[3]=n=>a.value.amount=n),type:"number",step:"0.01",class:T(["form-input",{error:c.value.amount}]),placeholder:"0.00",required:""},null,2),[[q,a.value.amount,void 0,{number:!0}]]),c.value.amount?(m(),p("span",ht,i(c.value.amount[0]),1)):v("",!0)])]),e("div",bt,[e("div",gt,[t[18]||(t[18]=e("label",{for:"currency"},"Валюта *",-1)),g(e("select",{id:"currency","onUpdate:modelValue":t[4]||(t[4]=n=>a.value.currency=n),class:"form-select",required:""},[...t[17]||(t[17]=[e("option",{value:"UZS"},"UZS",-1),e("option",{value:"USD"},"USD",-1)])],512),[[R,a.value.currency]])])]),e("div",wt,[t[19]||(t[19]=e("label",{for:"purpose"},"Назначение платежа",-1)),g(e("textarea",{id:"purpose","onUpdate:modelValue":t[5]||(t[5]=n=>a.value.purpose=n),class:"form-textarea",rows:"3",placeholder:"Введите назначение платежа"},null,512),[[q,a.value.purpose]])]),M.value?(m(),p("div",$t,[e("div",xt,[t[20]||(t[20]=e("span",null,"Сумма:",-1)),e("span",kt,i(h(a.value.amount))+" "+i(a.value.currency),1)]),e("div",Ct,[t[21]||(t[21]=e("span",null,"Комиссия:",-1)),e("span",Ut,i(h(M.value))+" "+i(a.value.currency),1)]),e("div",Vt,[t[22]||(t[22]=e("span",null,"Итого:",-1)),e("span",Mt,i(h(w.value))+" "+i(a.value.currency),1)])])):v("",!0),w.value>0?(m(),Ve(lt,{key:1,"required-total":w.value,"onUpdate:methods":F,onValidationChange:r},null,8,["required-total"])):v("",!0),u.value?(m(),p("div",qt,i(u.value),1)):v("",!0),e("div",Ft,[e("button",{type:"submit",class:"btn btn-primary",disabled:y.value||!z.value},i(y.value?"Создание...":"Создать платеж"),9,Pt),N(C,{to:"/payments",class:"btn btn-secondary"},{default:he(()=>[...t[23]||(t[23]=[B(" Отмена ",-1)])]),_:1})])],32)]),f.value?(m(),p("div",{key:0,class:"modal-overlay",onClick:t[8]||(t[8]=n=>f.value=!1)},[e("div",{class:"modal-content",onClick:t[7]||(t[7]=S(()=>{},["stop"]))},[t[31]||(t[31]=e("div",{class:"modal-header"},[e("h2",null,"Подтверждение платежа")],-1)),e("div",Tt,[t[30]||(t[30]=e("p",{class:"confirm-question"},"Вы уверены, что хотите провести платеж?",-1)),e("div",Dt,[e("div",zt,[t[24]||(t[24]=e("span",{class:"detail-label"},"Плательщик:",-1)),e("span",Et,i(a.value.payer_name),1)]),e("div",Rt,[t[25]||(t[25]=e("span",{class:"detail-label"},"Тип платежа:",-1)),e("span",At,i((x=$.value)==null?void 0:x.name),1)]),e("div",It,[t[26]||(t[26]=e("span",{class:"detail-label"},"Сумма:",-1)),e("span",Nt,i(h(a.value.amount))+" "+i(a.value.currency),1)]),e("div",St,[t[27]||(t[27]=e("span",{class:"detail-label"},"Комиссия:",-1)),e("span",Bt,i(h(M.value))+" "+i(a.value.currency),1)]),e("div",Lt,[t[28]||(t[28]=e("span",{class:"detail-label"},"Итого:",-1)),e("span",jt,i(h(w.value))+" "+i(a.value.currency),1)]),a.value.method_details.length>0?(m(),p("div",Qt,[t[29]||(t[29]=e("div",{class:"methods-header"},"Методы оплаты:",-1)),(m(!0),p(L,null,j(a.value.method_details,(n,E)=>(m(),p("div",{key:E,class:"method-row"},[e("span",Zt,i(l(n.method))+":",1),e("span",Ht,i(h(n.amount))+" "+i(a.value.currency),1),n.payment_system?(m(),p("span",Kt,"("+i(n.payment_system.toUpperCase())+")",1)):v("",!0)]))),128))])):v("",!0)])]),e("div",Wt,[e("button",{onClick:_,class:"btn btn-primary",disabled:y.value},i(y.value?"Обработка...":"Подтвердить"),9,Ot),e("button",{onClick:t[6]||(t[6]=n=>f.value=!1),class:"btn btn-secondary",disabled:y.value}," Отмена ",8,Gt)])])])):v("",!0),V.value?(m(),p("div",{key:1,class:"modal-overlay",onClick:Q},[e("div",{class:"modal-content receipt-modal",onClick:t[9]||(t[9]=S(()=>{},["stop"]))},[t[32]||(t[32]=e("div",{class:"modal-header"},[e("h2",null,"Квитанция об оплате")],-1)),e("div",Jt,[N(Fe,{payment:o.value,"company-name":"Paynes Kassa","show-q-r-code":!1},null,8,["payment"])]),e("div",{class:"modal-footer"},[e("button",{onClick:ge,class:"btn btn-primary"}," Печать квитанции "),e("button",{onClick:Q,class:"btn btn-secondary"}," Закрыть ")])])])):v("",!0)])}}},aa=be(Xt,[["__scopeId","data-v-23523c03"]]);export{aa as default};
