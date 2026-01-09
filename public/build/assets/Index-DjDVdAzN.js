import{q as Q,r as f,D as V,c as X,y as tt,a as d,o as m,b as t,h as S,i as C,k as L,B as j,p as M,t as u,n as g,F as et,e as nt,j as P,z as Z,s as q}from"./app-CmJRaKAb.js";import{_ as ot}from"./_plugin-vue_export-helper-DlAUqK2U.js";const at={class:"incashes-container"},st={class:"page-header"},lt={class:"header-actions"},it={class:"filters-card"},ut={class:"filters"},ct={class:"filter-group"},rt={class:"filter-group"},dt={class:"summary-cards"},mt={class:"summary-card income"},vt={class:"summary-info"},pt={class:"summary-value"},bt={class:"summary-count"},ft={class:"summary-card expense"},yt={class:"summary-info"},_t={class:"summary-value"},gt={class:"summary-count"},xt={class:"summary-card balance"},$t={class:"summary-info"},ht={class:"summary-count"},kt={class:"table-card"},Ut={key:0,class:"loading"},wt={key:1,class:"empty-state"},St={key:2,class:"incashes-table"},Ct={class:"actions"},Zt=["onClick"],Dt=["onClick"],Et={key:3,class:"pagination"},Tt=["disabled"],Ft={class:"page-info"},zt=["disabled"],It={class:"modal-content"},Bt={class:"modal-header"},Nt={class:"form-group"},Rt={class:"form-group"},At={class:"modal-footer"},Vt=["disabled"],Lt={__name:"Index",setup(jt){const D=Q(),E=f(!1),k=f(!1),_=f([]),v=f(1),x=f(1),O=f(0),T=f(!1),F=f("income"),r=V({date:"",account_number:""}),i=V({account_number:"",amount:0,type:"income"}),s=X(()=>{const o={income:0,expense:0,income_count:0,expense_count:0,total_count:0,balance:0};return _.value.forEach(n=>{n.type==="income"?(o.income+=parseFloat(n.amount),o.income_count++):(o.expense+=parseFloat(n.amount),o.expense_count++),o.total_count++}),o.balance=o.income-o.expense,o});async function p(){var o,n;E.value=!0;try{const e={page:v.value,per_page:20,...r},a=await Z.get("/incashes",{params:e});a.data.success&&(_.value=a.data.data,O.value=((o=a.data.meta)==null?void 0:o.total)||0,x.value=((n=a.data.meta)==null?void 0:n.last_page)||1)}catch(e){console.error("Error loading incashes:",e),alert("Ошибка при загрузке инкассаций")}finally{E.value=!1}}function B(o){F.value=o,i.type=o,i.account_number="",i.amount=0,T.value=!0}function U(){T.value=!1}async function N(){var o,n;if(!i.account_number||i.amount<=0){y("Заполните все обязательные поля","error");return}if(i.type==="expense"){const e=s.value.balance;if(i.amount>e&&!confirm(`⚠️ Предупреждение!

Расход (${c(i.amount)} UZS) превышает текущий баланс (${c(e)} UZS).

Баланс станет отрицательным: ${c(e-i.amount)} UZS

Продолжить?`))return}k.value=!0;try{(await Z.post("/incashes",{account_number:i.account_number,amount:i.amount,type:i.type})).data.success&&(y(`${i.type==="income"?"✅ Приход":"⚠️ Расход"} успешно создан на сумму ${c(i.amount)} UZS`,"success"),U(),await p())}catch(e){console.error("Error creating incash:",e),y(((n=(o=e.response)==null?void 0:o.data)==null?void 0:n.message)||"Ошибка при создании инкассации","error")}finally{k.value=!1}}async function W(o){var n,e;if(confirm("Подтвердить инкассацию?"))try{(await Z.post(`/incashes/${o}/confirm`)).data.success&&(y("✅ Инкассация успешно подтверждена","success"),await p())}catch(a){console.error("Error confirming incash:",a),y(((e=(n=a.response)==null?void 0:n.data)==null?void 0:e.message)||"Ошибка при подтверждении","error")}}async function Y(o){var n,e;if(confirm("Удалить инкассацию?"))try{(await Z.delete(`/incashes/${o}`)).data.success&&(y("🗑️ Инкассация удалена","info"),await p())}catch(a){console.error("Error deleting incash:",a),y(((e=(n=a.response)==null?void 0:n.data)==null?void 0:e.message)||"Ошибка при удалении","error")}}function y(o,n="info"){const e=document.createElement("div");e.className=`notification notification-${n}`,e.textContent=o,document.body.appendChild(e),setTimeout(()=>{e.classList.add("show")},10),setTimeout(()=>{e.classList.remove("show"),setTimeout(()=>{document.body.removeChild(e)},300)},3e3)}function R(o){o<1||o>x.value||(v.value=o,p())}function G(){r.date="",r.account_number="",v.value=1,p()}function c(o){return new Intl.NumberFormat("ru-RU",{minimumFractionDigits:2,maximumFractionDigits:2}).format(o||0)}function $(o){if(!o)return"";const n=new Date(o);return new Intl.DateTimeFormat("ru-RU",{year:"numeric",month:"2-digit",day:"2-digit"}).format(n)}function h(o){return{"001":"001 - Наличные UZS","002":"002 - Безналичные UZS","003":"003 - Наличные RUB",840:"840 - Наличные USD"}[o]||o}function z(o){return{pending:"Ожидание",confirmed:"Подтверждено",deleted:"Удалено"}[o]||o}function H(){var a;const o=window.open("","_blank"),n=r.date?`за ${$(r.date)}`:"за весь период",e=r.account_number?` по счету ${h(r.account_number)}`:"";o.document.write(`
    <!DOCTYPE html>
    <html>
    <head>
      <title>Отчет по инкассации</title>
      <meta charset="utf-8">
      <style>
        @page { size: A4; margin: 15mm; }
        body { font-family: Arial, sans-serif; font-size: 12px; }
        h1 { text-align: center; font-size: 18px; margin-bottom: 5px; }
        h2 { text-align: center; font-size: 14px; margin-top: 0; color: #666; }
        .summary { display: flex; justify-content: space-around; margin: 20px 0; }
        .summary-item { text-align: center; padding: 10px; border: 1px solid #ddd; border-radius: 5px; flex: 1; margin: 0 5px; }
        .summary-item.income { background: #f0fdf4; }
        .summary-item.expense { background: #fef2f2; }
        .summary-item.balance { background: #dbeafe; }
        .summary-label { font-size: 11px; color: #666; }
        .summary-value { font-size: 16px; font-weight: bold; margin: 5px 0; }
        .summary-count { font-size: 10px; color: #999; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background: #f9fafb; font-weight: bold; }
        tr.income { background: #f0fdf4; }
        tr.expense { background: #fef2f2; }
        .amount.income { color: #10b981; }
        .amount.expense { color: #ef4444; }
        .footer { margin-top: 30px; display: flex; justify-content: space-between; font-size: 11px; }
        @media print {
          button { display: none; }
        }
      </style>
    </head>
    <body>
      <h1>Отчет по инкассации</h1>
      <h2>${n}${e}</h2>

      <div class="summary">
        <div class="summary-item income">
          <div class="summary-label">Приход</div>
          <div class="summary-value">${c(s.value.income)} UZS</div>
          <div class="summary-count">${s.value.income_count} операций</div>
        </div>
        <div class="summary-item expense">
          <div class="summary-label">Расход</div>
          <div class="summary-value">${c(s.value.expense)} UZS</div>
          <div class="summary-count">${s.value.expense_count} операций</div>
        </div>
        <div class="summary-item balance">
          <div class="summary-label">Баланс</div>
          <div class="summary-value">${c(s.value.balance)} UZS</div>
          <div class="summary-count">${s.value.total_count} операций</div>
        </div>
      </div>

      <table>
        <thead>
          <tr>
            <th>№</th>
            <th>Дата</th>
            <th>Время</th>
            <th>Тип</th>
            <th>Счет</th>
            <th>Сумма</th>
            <th>Кассир</th>
            <th>Статус</th>
          </tr>
        </thead>
        <tbody>
          ${_.value.map((l,I)=>{var w;return`
            <tr class="${l.type}">
              <td>${I+1}</td>
              <td>${$(l.date)}</td>
              <td>${l.time}</td>
              <td>${l.type==="income"?"↓ Приход":"↑ Расход"}</td>
              <td>${h(l.account_number)}</td>
              <td class="amount ${l.type}">${l.type==="income"?"+":"-"}${c(l.amount)}</td>
              <td>${((w=l.cashier)==null?void 0:w.full_name)||"-"}</td>
              <td>${z(l.status)}</td>
            </tr>
          `}).join("")}
        </tbody>
      </table>

      <div class="footer">
        <div>Дата печати: ${new Date().toLocaleString("ru-RU")}</div>
        <div>Пользователь: ${((a=D.user)==null?void 0:a.full_name)||"-"}</div>
      </div>

      <script>
        window.onload = function() {
          window.print();
        };
      <\/script>
    </body>
    </html>
  `),o.document.close()}function J(){const o=r.date?`за ${$(r.date)}`:"за весь период",n=r.account_number?` по счету ${h(r.account_number)}`:"";let e="\uFEFF";e+=`Отчет по инкассации ${o}${n}

`,e+=`Сводка
`,e+=`Приход,${c(s.value.income)} UZS,${s.value.income_count} операций
`,e+=`Расход,${c(s.value.expense)} UZS,${s.value.expense_count} операций
`,e+=`Баланс,${c(s.value.balance)} UZS,${s.value.total_count} операций

`,e+=`Детализация
`,e+=`№,Дата,Время,Тип,Счет,Сумма,Кассир,Статус
`,_.value.forEach((b,K)=>{var A;e+=`${K+1},`,e+=`${$(b.date)},`,e+=`${b.time},`,e+=`${b.type==="income"?"Приход":"Расход"},`,e+=`${h(b.account_number)},`,e+=`${b.type==="income"?"+":"-"}${c(b.amount)},`,e+=`${((A=b.cashier)==null?void 0:A.full_name)||"-"},`,e+=`${z(b.status)}
`});const a=new Blob([e],{type:"text/csv;charset=utf-8;"}),l=document.createElement("a"),I=URL.createObjectURL(a),w=`incash_report_${new Date().toISOString().slice(0,10)}.csv`;l.setAttribute("href",I),l.setAttribute("download",w),l.style.visibility="hidden",document.body.appendChild(l),l.click(),document.body.removeChild(l)}return tt(()=>{p()}),(o,n)=>(m(),d("div",at,[t("div",st,[n[8]||(n[8]=t("h1",null,"Инкассация",-1)),t("div",lt,[t("button",{onClick:H,class:"btn btn-print",title:"Печать отчета"}," 🖨️ Печать отчета "),t("button",{onClick:J,class:"btn btn-export",title:"Экспорт в Excel"}," 📊 Excel "),t("button",{onClick:n[0]||(n[0]=e=>B("income")),class:"btn btn-success"}," ↓ Приход "),t("button",{onClick:n[1]||(n[1]=e=>B("expense")),class:"btn btn-danger"}," ↑ Расход ")])]),t("div",it,[t("div",ut,[t("div",ct,[n[9]||(n[9]=t("label",null,"Дата:",-1)),C(t("input",{type:"date","onUpdate:modelValue":n[2]||(n[2]=e=>r.date=e),onChange:p},null,544),[[L,r.date]])]),t("div",rt,[n[11]||(n[11]=t("label",null,"Счет:",-1)),C(t("select",{"onUpdate:modelValue":n[3]||(n[3]=e=>r.account_number=e),onChange:p},[...n[10]||(n[10]=[M('<option value="" data-v-ad94c1ee>Все</option><option value="001" data-v-ad94c1ee>001 - Наличные UZS</option><option value="002" data-v-ad94c1ee>002 - Безналичные UZS</option><option value="003" data-v-ad94c1ee>003 - Наличные RUB</option><option value="840" data-v-ad94c1ee>840 - Наличные USD</option>',5)])],544),[[j,r.account_number]])]),t("button",{onClick:G,class:"btn btn-secondary"},"Сбросить")])]),t("div",dt,[t("div",mt,[n[13]||(n[13]=t("div",{class:"summary-icon"},"↓",-1)),t("div",vt,[n[12]||(n[12]=t("div",{class:"summary-label"},"Приход",-1)),t("div",pt,u(c(s.value.income))+" UZS",1),t("div",bt,u(s.value.income_count)+" операций",1)])]),t("div",ft,[n[15]||(n[15]=t("div",{class:"summary-icon"},"↑",-1)),t("div",yt,[n[14]||(n[14]=t("div",{class:"summary-label"},"Расход",-1)),t("div",_t,u(c(s.value.expense))+" UZS",1),t("div",gt,u(s.value.expense_count)+" операций",1)])]),t("div",xt,[n[17]||(n[17]=t("div",{class:"summary-icon"},"≈",-1)),t("div",$t,[n[16]||(n[16]=t("div",{class:"summary-label"},"Баланс",-1)),t("div",{class:g(["summary-value",{negative:s.value.balance<0}])},u(c(s.value.balance))+" UZS ",3),t("div",ht,u(s.value.total_count)+" операций",1)])])]),t("div",kt,[E.value?(m(),d("div",Ut,"Загрузка...")):_.value.length===0?(m(),d("div",wt," Нет инкассаций за выбранный период ")):(m(),d("table",St,[n[18]||(n[18]=t("thead",null,[t("tr",null,[t("th",null,"Дата"),t("th",null,"Время"),t("th",null,"Тип"),t("th",null,"Счет"),t("th",null,"Сумма"),t("th",null,"Кассир"),t("th",null,"Статус"),t("th",null,"Действия")])],-1)),t("tbody",null,[(m(!0),d(et,null,nt(_.value,e=>{var a;return m(),d("tr",{key:e.id,class:g(e.type)},[t("td",null,u($(e.date)),1),t("td",null,u(e.time),1),t("td",null,[t("span",{class:g(["type-badge",e.type])},u(e.type==="income"?"↓ Приход":"↑ Расход"),3)]),t("td",null,u(h(e.account_number)),1),t("td",{class:g(["amount",e.type])},u(e.type==="income"?"+":"-")+u(c(e.amount)),3),t("td",null,u((a=e.cashier)==null?void 0:a.full_name),1),t("td",null,[t("span",{class:g(["status-badge",e.status])},u(z(e.status)),3)]),t("td",Ct,[q(D).isAdmin&&e.status==="pending"?(m(),d("button",{key:0,onClick:l=>W(e.id),class:"btn-icon btn-success",title:"Подтвердить"}," ✓ ",8,Zt)):S("",!0),q(D).isAdmin?(m(),d("button",{key:1,onClick:l=>Y(e.id),class:"btn-icon btn-danger",title:"Удалить"}," × ",8,Dt)):S("",!0)])],2)}),128))])])),x.value>1?(m(),d("div",Et,[t("button",{onClick:n[4]||(n[4]=e=>R(v.value-1)),disabled:v.value===1,class:"btn btn-secondary"}," ← Назад ",8,Tt),t("span",Ft,"Страница "+u(v.value)+" из "+u(x.value),1),t("button",{onClick:n[5]||(n[5]=e=>R(v.value+1)),disabled:v.value===x.value,class:"btn btn-secondary"}," Вперед → ",8,zt)])):S("",!0)]),T.value?(m(),d("div",{key:0,class:"modal-overlay",onClick:P(U,["self"])},[t("div",It,[t("div",Bt,[t("h3",null,u(F.value==="income"?"Приход (Инкассация)":"Расход (Выдача)"),1),t("button",{onClick:U,class:"close-btn"},"×")]),t("form",{onSubmit:P(N,["prevent"]),class:"modal-body"},[t("div",Nt,[n[20]||(n[20]=t("label",null,"Счет: *",-1)),C(t("select",{"onUpdate:modelValue":n[6]||(n[6]=e=>i.account_number=e),required:""},[...n[19]||(n[19]=[M('<option value="" data-v-ad94c1ee>Выберите счет</option><option value="001" data-v-ad94c1ee>001 - Наличные UZS</option><option value="002" data-v-ad94c1ee>002 - Безналичные UZS</option><option value="003" data-v-ad94c1ee>003 - Наличные RUB</option><option value="840" data-v-ad94c1ee>840 - Наличные USD</option>',5)])],512),[[j,i.account_number]])]),t("div",Rt,[n[21]||(n[21]=t("label",null,"Сумма: *",-1)),C(t("input",{type:"number","onUpdate:modelValue":n[7]||(n[7]=e=>i.amount=e),step:"0.01",min:"0",required:"",placeholder:"0.00"},null,512),[[L,i.amount,void 0,{number:!0}]])])],32),t("div",At,[t("button",{onClick:U,class:"btn btn-secondary"},"Отмена"),t("button",{onClick:N,disabled:k.value,class:g(["btn",F.value==="income"?"btn-success":"btn-danger"])},u(k.value?"Создание...":"Создать"),11,Vt)])])])):S("",!0)]))}},qt=ot(Lt,[["__scopeId","data-v-ad94c1ee"]]);export{qt as default};
