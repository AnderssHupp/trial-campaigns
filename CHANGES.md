PRIMEIRO ERROR: MODEL CONTACT NÃO EXISTE, IMPOSIBILITANDO A SEEDIND DA DATABASE, MIGRATIONS E DEFINICAO DE CAMPOS
model campaing send nao existe
model contact list nao existe

-criação destes model e definição de suas relações, campos, e protected e casts

Campaing send não tinha o import dos ContactList e nem do CampaingSend ate pq nao estavam definidos

Criação do campaing send, definicao dos campos fillable e cast e de sua table,  e definição de suas relações outros models.

ERRO DE NAO POSSUIR MODELS DEFINIDOS: Ao rodar qualquer código que referencie esses models, o Laravel lançará erros de classe inexistente, Sendo assim qualquer job em fila falhariam imediatamente ao tentar acessar $send->contact ou $send->campaign sem o model definido.

ERROR NO MIDDLEWARE EnsureCampaingIsDraft, logica invetida. 
o middleware ira rejeitar requisições quando a campanha estiver draft e permitir as demais.

correção trocar o === por != diferente

Campaing Service não trata possiveis campos null (), solucao: tratar compos null

campo schelduled_at na migration create_campaing como tipo sting, não é propiamente um error. mas melhor definir como date time, para facilitar buscas, ordenação e etc.

campo contact sem unique, possibilitando duplicação de emails.

SendCampaingEmail, apenas simula o envio de email, em produção deve criar o Mail;

Falha na tabela pivot contact_contact_list não tem uma constraint de unicidade em para contact_id, contact_list_id. Isso significa que o mesmo contato pode ser associado várias vezes à mesma lista.

correcçao: definir como unique

