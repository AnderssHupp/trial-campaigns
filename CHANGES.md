## Modelos inexistentes (Contact, CampaignSend, ContactList)

- **Problema**  
  Os models `Contact`, `CampaignSend` e `ContactList` não existiam inicialmente, impossibilitando o seeding da base, o uso das migrations e a definição adequada de campos e relações.

- **Impacto em produção**  
  Qualquer código que referencie esses models (por exemplo, jobs em fila acessando `$send->contact` ou `$send->campaign`) causaria erros de classe inexistente, quebrando o fluxo de envio de campanhas e tornando o sistema incapaz de manipular contatos, listas e envios.

- **Correção aplicada**  
  Foram criados os models `Contact`, `ContactList` e `CampaignSend`, com definição de:
  - campos `fillable` e `casts` apropriados;  
  - relações entre eles (belongsTo, hasMany, belongsToMany);  
  - tabela associada quando necessário (ex.: `campaign_sends`);  
  - factories correspondentes para facilitar testes e seeders.

---

## Imports e relações em CampaignSend

- **Problema**  
  O model `CampaignSend`, além de não existir inicialmente, não possuía os imports para `Contact` e `Campaign`, nem as relações definidas.

- **Impacto em produção**  
  Jobs/serviços que precisassem navegar de um envio para a campanha ou para o contato (`$send->campaign`, `$send->contact`) falhariam, impedindo o fluxo de disparo de campanhas ou qualquer relatório baseado nesses relacionamentos.

- **Correção aplicada**  
  O model `CampaignSend` foi criado com:
  - imports corretos para `Contact` e `Campaign`;  
  - definição de `fillable` e `casts`;  
  - definição explícita da tabela `campaign_sends`;  
  - relações `belongsTo` para `campaign` e `contact`.

---

## Middleware EnsureCampaignIsDraft com lógica invertida

- **Problema**  
  O middleware `EnsureCampaignIsDraft` continha a lógica invertida: ele rejeitava requisições quando a campanha estava em estado `draft` e permitia quando não estava.

- **Impacto em produção**  
  A proteção de fluxo de edição/disparo de campanhas ficaria incorreta, bloqueando ações justamente quando a campanha ainda está em rascunho (estado em que deveria poder ser alterada) e permitindo alterações em estados incorretos, abrindo margem para inconsistências de negócio.

- **Correção aplicada**  
  A comparação foi ajustada de `===` para `!=`, garantindo que o middleware **só bloqueie** quando a campanha não estiver em estado `draft`, e permita as requisições quando estiver `draft`.

---

## Tratamento de campos nulos em CampaignService

- **Problema**  
  O `CampaignService` não tratava adequadamente possíveis campos nulos da campanha ao montar payloads ou resolver informações opcionais (como `reply_to`), o que podia gerar acessos a valores nulos.

- **Impacto em produção**  
  Em cenários em que determinados campos não fossem preenchidos, o serviço poderia lançar erros, interromper o envio de campanhas ou produzir payloads incompletos/inválidos.

- **Correção aplicada**  
  Foram adicionadas verificações para campos opcionais (por exemplo, em `resolveReplyTo`), garantindo que campos nulos sejam tratados de forma segura, retornando `null` ou valores padrão quando apropriado.

---

## Tipo da coluna scheduled_at em campaigns

- **Problema**  
  Na migration `create_campaigns`, o campo `scheduled_at` estava definido como `string` em vez de tipo datetime.

- **Impacto em produção**  
  Manter datas como string dificulta ordenação, filtros por período, consultas de campanhas “vencidas” ou “agendadas” e pode gerar inconsistências de formatação, além de prejudicar uso de índices de data/hora.

- **Correção aplicada**  
  O campo `scheduled_at` foi alterado para um tipo datetime adequado, permitindo ordenação, filtros e uso dos recursos nativos do banco para datas (incluindo índices e comparações eficientes).

---

## Falta de unique no email de contacts

- **Problema**  
  A tabela/validação de `contacts` não garantia unicidade do campo `email`.

- **Impacto em produção**  
  Isso permitia múltiplos registros com o mesmo email, o que pode gerar envios duplicados para o mesmo destinatário, relatórios imprecisos e dificuldade para gerenciar preferências (unsubscribe, status, etc.).

- **Correção aplicada**  
  Foi adicionada a constraint de unicidade para o campo `email` (tanto na migration quanto na validação), garantindo que cada email exista apenas uma vez em `contacts`.

---

## Falta de unicidade na pivot contact_contact_list

- **Problema**  
  A tabela pivot `contact_contact_list` não possuía uma constraint de unicidade para o par (`contact_id`, `contact_list_id`).

- **Impacto em produção**  
  O mesmo contato poderia ser associado várias vezes à mesma lista, gerando duplicidade em relatórios, múltiplos envios para o mesmo email dentro de uma campanha e inconsistências de negócio.

- **Correção aplicada**  
  Foi adicionada uma constraint `unique` na combinação (`contact_id`, `contact_list_id`) na migration, garantindo que cada contato só possa aparecer uma vez por lista.

---

## Cálculo de stats em Campaign usando coleção em vez de agregação no banco

- **Problema**  
  O método `getStatsAttribute` no model `Campaign` fazia:
  - `$sends = $this->sends;`
  - contagens via coleção (`$sends->where('status', ...)->count()`).

  Isso viola oque é pedido no enunciado além de prejudicar a perfomance carregando todos os registros de `campaign_sends` em memória para depois filtrar.

- **Impacto em produção**  
  Para campanhas com muitos envios:
  - aumento significativo de consumo de memória e CPU na aplicação;  
  - maior risco de timeouts ou “estouro” de memória;  
  - dificuldade de escalar o sistema conforme o volume de envios cresce.

- **Correção aplicada**  
  O método foi reescrito para usar agregação diretamente no banco:
  - `sends()->selectRaw('status, COUNT(*) as aggregate')->groupBy('status')` com `pluck` para obter as contagens por status;  
  - cálculo de `pending`, `sent`, `failed` e `total` a partir desses valores agregados.

  Agora:
  - a query retorna apenas até 3 linhas (uma por status), em vez de todos os envios;  
  - toda a lógica de contagem acontece no banco;  
  - o PHP apenas lê os resultados já agregados, melhorando performance e escalabilidade.

