const __vite__mapDeps=(i,m=__vite__mapDeps,d=(m.f||(m.f=["assets/PaymentReceipt-BLWLkO8i.js","assets/app-DmhU3QBQ.js","assets/app-DfUGuj8f.css","assets/_plugin-vue_export-helper-DlAUqK2U.js","assets/PaymentReceipt-D6f2BIQq.css"])))=>i.map(i=>d[i]);
import{r as p,q as ut,y as ct,z as h,a as r,o,b as e,i as f,d as pt,w as mt,f as vt,j as X,B as ft,g as gt,l as bt,t as i,F as ht,e as yt,A as _t,k as wt,_ as xt,h as kt,n as $t,s as $}from"./app-DmhU3QBQ.js";import{P as Ct}from"./PaymentReceipt-BLWLkO8i.js";import{_ as zt}from"./_plugin-vue_export-helper-DlAUqK2U.js";const Dt={class:"payments-page"},Ft={class:"page-header"},Rt={class:"filters-card"},Pt={class:"filters"},Vt={class:"filter-group"},At={class:"filter-group"},Tt={key:0,class:"loading"},Ut={key:1,class:"alert alert-error"},It={key:2,class:"empty-state"},Nt={key:3,class:"table-card"},qt={class:"data-table"},St={class:"text-small"},Bt={class:"font-weight-bold"},Lt={class:"action-buttons"},Mt=["onClick"],jt=["onClick"],Qt=["onClick"],Et=["onClick"],Kt={key:0,class:"pagination"},Ht=["disabled"],Ot={class:"pagination-info"},Wt=["disabled"],Gt={class:"modal-body"},Jt={__name:"Index",setup(Xt){const _=p([]),u=p(null),w=p(!1),y=p(null),d=p({status:"",date:"",page:1}),x=p(!1),n=p(null),k=ut();ct(()=>{c()});async function c(){var s,t;w.value=!0,y.value=null;try{const l=new URLSearchParams;d.value.status&&l.append("status",d.value.status),d.value.date&&l.append("date",d.value.date),l.append("page",d.value.page);const a=await h.get(`/payments?${l}`);a.data.success&&(_.value=a.data.data.data,u.value=a.data.data.meta)}catch(l){y.value=((t=(s=l.response)==null?void 0:s.data)==null?void 0:t.message)||"Ошибка загрузки платежей"}finally{w.value=!1}}function Y(){d.value={status:"",date:"",page:1},c()}function C(s){d.value.page=s,c()}async function Z(s){var t,l;if(confirm("Подтвердить платеж?"))try{await h.post(`/payments/${s}/confirm`),await c()}catch(a){alert(((l=(t=a.response)==null?void 0:t.data)==null?void 0:l.message)||"Ошибка подтверждения платежа")}}async function tt(s){var t,l;if(confirm("Создать дубликат платежа?"))try{await h.post(`/payments/${s}/duplicate`),await c()}catch(a){alert(((l=(t=a.response)==null?void 0:t.data)==null?void 0:l.message)||"Ошибка создания дубликата")}}async function et(s){var t,l;if(confirm("Удалить платеж?"))try{await h.delete(`/payments/${s}`),await c()}catch(a){alert(((l=(t=a.response)==null?void 0:t.data)==null?void 0:l.message)||"Ошибка удаления платежа")}}function m(s){return new Intl.NumberFormat("ru-RU",{minimumFractionDigits:2,maximumFractionDigits:2}).format(s)}function at(s){return{pending:"Ожидание",confirmed:"Подтвержден",deleted:"Удален",processed:"Обработан"}[s]||s}function nt(s){return{pending:"warning",confirmed:"success",deleted:"danger",processed:"info"}[s]||"default"}async function st(s){var t,l;try{const a=await h.get(`/payments/${s}`);a.data.success&&(n.value=a.data.data,x.value=!0)}catch(a){alert(((l=(t=a.response)==null?void 0:t.data)==null?void 0:l.message)||"Ошибка загрузки данных платежа")}}function z(){x.value=!1,n.value=null}async function lt(){var a,g,b,v,D,F,R,P,V,A,T,U,I,N,q,S,B,L,M,j,Q,E,K,H,O,W,G;if(!n.value)return;let s="";if((a=n.value)!=null&&a.share_url)try{s=await(await xt(async()=>{const{default:rt}=await import("./PaymentReceipt-BLWLkO8i.js").then(dt=>dt.b);return{default:rt}},__vite__mapDeps([0,1,2,3,4]))).default.toDataURL(n.value.share_url,{width:60,margin:1,color:{dark:"#000000",light:"#FFFFFF"}})}catch(J){console.error("QR generation failed:",J)}const t=window.open("","_blank"),l=`
    <html>
      <head>
        <title>Дубликат квитанции - ${(g=n.value)==null?void 0:g.payment_number}</title>
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
            position: relative;
            overflow: hidden;
          }

          .duplicate-watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 32pt;
            font-weight: bold;
            color: rgba(255, 0, 0, 0.15);
            z-index: 10;
            pointer-events: none;
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

            .duplicate-watermark {
              color: rgba(255, 0, 0, 0.15) !important;
            }
          }
        </style>
      </head>
      <body>
        <div class="receipt-wrapper">
          <div class="receipt-container">
            <div class="duplicate-watermark">ДУБЛИКАТ</div>
            <div class="receipt-left">
              <h4 class="receipt-label">Хабарнома</h4>
              <h4 class="receipt-label">Paynes Kassa</h4>
              <h4 class="receipt-label">${((b=n.value)==null?void 0:b.payment_method)==="cash"?"Наличный":"Безналичный"}</h4>
              <h4 class="receipt-label">${it((v=n.value)==null?void 0:v.created_at)}</h4>
              <h4 class="receipt-label">${ot((D=n.value)==null?void 0:D.created_at)}</h4>
              ${s?`
                <div class="qr-code">
                  <img src="${s}" alt="QR Code" />
                </div>
              `:""}
              <h4 class="receipt-label">№ ${((F=n.value)==null?void 0:F.payment_number)||""}</h4>
              <h4 class="receipt-label">${((P=(R=n.value)==null?void 0:R.cashier)==null?void 0:P.full_name)||""}</h4>

              <div class="signature-field">
                <div class="signature-label">Подпись:</div>
                <div class="signature-line"></div>
              </div>
            </div>

            <div class="receipt-right">
              <h4 class="receipt-header">${((A=(V=n.value)==null?void 0:V.payment_type)==null?void 0:A.name)||""}</h4>

              <div class="receipt-details">
                ${(U=(T=n.value)==null?void 0:T.payment_type)!=null&&U.bank_name?`
                  <div class="detail-line">
                    <span class="detail-label">Банк:</span>
                    <span class="detail-value">${n.value.payment_type.bank_name}</span>
                  </div>
                `:""}
                ${(N=(I=n.value)==null?void 0:I.payment_type)!=null&&N.account_number?`
                  <div class="detail-line">
                    <span class="detail-label">Счет №:</span>
                    <span class="detail-value">${n.value.payment_type.account_number}</span>
                  </div>
                `:""}
                ${(S=(q=n.value)==null?void 0:q.payment_type)!=null&&S.mfo?`
                  <div class="detail-line">
                    <span class="detail-label">МФО:</span>
                    <span class="detail-value">${n.value.payment_type.mfo}</span>
                  </div>
                `:""}
                ${(L=(B=n.value)==null?void 0:B.payment_type)!=null&&L.inn?`
                  <div class="detail-line">
                    <span class="detail-label">ИНН:</span>
                    <span class="detail-value">${n.value.payment_type.inn}</span>
                  </div>
                `:""}
                ${(M=n.value)!=null&&M.list_number?`
                  <div class="detail-line">
                    <span class="detail-label">Лицевой счет:</span>
                    <span class="detail-value">${n.value.list_number}</span>
                  </div>
                `:""}
              </div>

              <h4 class="payer-name">${((j=n.value)==null?void 0:j.payer_name)||""}</h4>

              ${(Q=n.value)!=null&&Q.purpose?`
                <div class="purpose-section">
                  <p class="purpose-text">${n.value.purpose}</p>
                  <p class="purpose-label">(Назначение платежа / Адрес)</p>
                </div>
              `:""}

              <div class="amounts-section">
                <div class="amount-row">
                  <span class="amount-label">Сумма:</span>
                  <span class="amount-value">${m((E=n.value)==null?void 0:E.amount)} ${((K=n.value)==null?void 0:K.currency)||""}</span>
                </div>
                <div class="amount-row">
                  <span class="amount-label">Комиссия:</span>
                  <span class="amount-value">${m((H=n.value)==null?void 0:H.commission_amount)} ${((O=n.value)==null?void 0:O.currency)||""}</span>
                </div>
                <div class="amount-row total-row">
                  <span class="amount-label">Итого:</span>
                  <span class="amount-value">${m((W=n.value)==null?void 0:W.total_amount)} ${((G=n.value)==null?void 0:G.currency)||""}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </body>
    </html>
  `;t.document.write(l),t.document.close(),t.focus(),setTimeout(()=>{t.print(),t.close()},250)}function it(s){if(!s)return"";const t=new Date(s);return new Intl.DateTimeFormat("ru-RU",{year:"numeric",month:"2-digit",day:"2-digit"}).format(t)}function ot(s){if(!s)return"";const t=new Date(s);return new Intl.DateTimeFormat("ru-RU",{hour:"2-digit",minute:"2-digit"}).format(t)}return(s,t)=>{const l=vt("router-link");return o(),r("div",Dt,[e("div",Ft,[t[6]||(t[6]=e("h1",{class:"page-title"},"Платежи",-1)),pt(l,{to:"/payments/create",class:"btn btn-primary"},{default:mt(()=>[...t[5]||(t[5]=[kt(" + Новый платеж ",-1)])]),_:1})]),e("div",Rt,[e("div",Pt,[e("div",Vt,[t[8]||(t[8]=e("label",null,"Статус",-1)),X(e("select",{"onUpdate:modelValue":t[0]||(t[0]=a=>d.value.status=a),onChange:c,class:"form-select"},[...t[7]||(t[7]=[gt('<option value="" data-v-aac71aaf>Все</option><option value="pending" data-v-aac71aaf>Ожидание</option><option value="confirmed" data-v-aac71aaf>Подтвержден</option><option value="deleted" data-v-aac71aaf>Удален</option><option value="processed" data-v-aac71aaf>Обработан</option>',5)])],544),[[ft,d.value.status]])]),e("div",At,[t[9]||(t[9]=e("label",null,"Дата",-1)),X(e("input",{type:"date","onUpdate:modelValue":t[1]||(t[1]=a=>d.value.date=a),onChange:c,class:"form-input"},null,544),[[bt,d.value.date]])]),e("button",{onClick:Y,class:"btn btn-secondary"},"Очистить")])]),w.value?(o(),r("div",Tt,"Загрузка...")):y.value?(o(),r("div",Ut,i(y.value),1)):_.value.length===0?(o(),r("div",It,[...t[10]||(t[10]=[e("p",null,"Платежи не найдены",-1)])])):(o(),r("div",Nt,[e("table",qt,[t[11]||(t[11]=e("thead",null,[e("tr",null,[e("th",null,"№"),e("th",null,"Дата/Время"),e("th",null,"Плательщик"),e("th",null,"Тип платежа"),e("th",null,"Сумма"),e("th",null,"Комиссия"),e("th",null,"Итого"),e("th",null,"Статус"),e("th",null,"Кассир"),e("th",null,"Действия")])],-1)),e("tbody",null,[(o(!0),r(ht,null,yt(_.value,a=>{var g,b;return o(),r("tr",{key:a.id},[e("td",null,i(a.list_number),1),e("td",null,[e("div",null,i(a.date),1),e("div",St,i(a.time),1)]),e("td",null,i(a.payer_name),1),e("td",null,i((g=a.payment_type)==null?void 0:g.name),1),e("td",null,i(m(a.amount))+" "+i(a.currency),1),e("td",null,i(m(a.commission))+" "+i(a.currency),1),e("td",Bt,i(m(a.total))+" "+i(a.currency),1),e("td",null,[e("span",{class:$t(["badge",`badge-${nt(a.status)}`])},i(at(a.status)),3)]),e("td",null,i((b=a.cashier)==null?void 0:b.full_name),1),e("td",null,[e("div",Lt,[e("button",{onClick:v=>st(a.id),class:"btn-action btn-print",title:"Печать квитанции"}," 🖨️ ",8,Mt),$(k).isAdmin&&a.status==="pending"?(o(),r("button",{key:0,onClick:v=>Z(a.id),class:"btn-action btn-success",title:"Подтвердить"}," ✓ ",8,jt)):f("",!0),$(k).isAdmin?(o(),r("button",{key:1,onClick:v=>tt(a.id),class:"btn-action btn-info",title:"Дублировать"}," 📋 ",8,Qt)):f("",!0),$(k).isAdmin&&a.status==="pending"?(o(),r("button",{key:2,onClick:v=>et(a.id),class:"btn-action btn-danger",title:"Удалить"}," × ",8,Et)):f("",!0)])])])}),128))])]),u.value?(o(),r("div",Kt,[e("button",{onClick:t[2]||(t[2]=a=>C(u.value.current_page-1)),disabled:u.value.current_page===1,class:"btn btn-secondary"}," Назад ",8,Ht),e("span",Ot," Страница "+i(u.value.current_page)+" из "+i(u.value.last_page),1),e("button",{onClick:t[3]||(t[3]=a=>C(u.value.current_page+1)),disabled:u.value.current_page===u.value.last_page,class:"btn btn-secondary"}," Вперед ",8,Wt)])):f("",!0)])),x.value?(o(),r("div",{key:4,class:"modal-overlay",onClick:z},[e("div",{class:"modal-content receipt-modal",onClick:t[4]||(t[4]=wt(()=>{},["stop"]))},[t[12]||(t[12]=e("div",{class:"modal-header"},[e("h2",null,"Дубликат квитанции")],-1)),e("div",Gt,[n.value?(o(),_t(Ct,{key:0,payment:n.value,"company-name":"Paynes Kassa","show-q-r-code":!1},null,8,["payment"])):f("",!0)]),e("div",{class:"modal-footer"},[e("button",{onClick:lt,class:"btn btn-primary"}," Печать дубликата "),e("button",{onClick:z,class:"btn btn-secondary"}," Закрыть ")])])])):f("",!0)])}}},ee=zt(Jt,[["__scopeId","data-v-aac71aaf"]]);export{ee as default};
