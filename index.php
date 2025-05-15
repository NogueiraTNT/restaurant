<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurant</title>
    <!-- Puxa o CSS do Bootstrap -->
    <link 
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" 
    rel="stylesheet" 
    integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" 
    crossorigin="anonymous">
</head>
<body>
    <!-- Barra de Menu do site -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Restaurant</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDarkDropdown" aria-controls="navbarNavDarkDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavDarkDropdown">
            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                <button class="btn btn-dark dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    Menu
                </button>
                <ul class="dropdown-menu dropdown-menu-dark">
                    <li>
                    <a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#exampleModal">
                        Cadastrar Produtos
                    </a>
                    </li>
                    <li>
                    <a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#exampleModalCompra">
                        Fazer um pedido
                    </a>
                    </li>
                    <li><a class="dropdown-item" href="#">Something else here</a></li>
                </ul>
                </li>
            </ul>
            </div>
        </div>
    </nav>

    <!-- Mostra os pedidos em ordem decrecente -->
    <div class="card">
        <h5 class="card-header">Pedidos</h5>
        <div class="card-body" id="carregar-pedidos">
        </div>
    </div>


    <!-- Modal de cadastro de Pedidos -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content" id="cadastro-produto">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Produtos</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="form-cadastro-produtos">
                    ...
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="cadastrarProduto()">Cadastrar novo</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Carrinho de Compras -->
    <div class="modal fade" id="exampleModalCompra" tabindex="-1" aria-labelledby="exampleModalCompraLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content" id="cadastro-produto">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalCompraLabel">Carrinho de Compra</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="carrinho-compra">
                    ...
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="comprar()">Finalizar Compra</button>
                    <button type="button" class="btn btn-primary" onclick="abrirCarrinho()">Comaçar a comprar</button>
                </div>
            </div>
        </div>
    </div>

<!-- Puxa o Script do Bootstrap -->
<script 
src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" 
integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" 
crossorigin="anonymous"></script>

<!-- script Principal -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    ///////Carrega os Pedidos no Index da Pagina
    fetch('./run/select-pedidos.php')
        .then(response => {
            if (!response.ok) throw new Error('Erro ao buscar os produtos');
            return response.json();
        })
        .then(data => {
            const carregarPedidos = document.getElementById("carregar-pedidos");
            carregarPedidos.innerHTML = '';
            ////////////Carrega todos os pedidos e escreve na Pagina
            data.forEach(pedidos => {
                carregarPedidos.innerHTML += `
                    <div class="card mb-3 p-3">
                        <h5 class="card-title">Numero #${pedidos.pedido_id}</h5>
                        <h6>${pedidos.pedido}</h6>
                        <p class="card-text">Valor do pedido: R$ ${parseFloat(pedidos.valor_total).toFixed(2)}</p>
                        <p class="card-text">Frete: R$ ${parseFloat(pedidos.frete).toFixed(2)}</p>
                        <p class="card-text">Total: R$ ${(parseFloat(pedidos.valor_total) + parseFloat(pedidos.frete)).toFixed(2)}</p>
                        <p class="card-text">Desconto aplicado: R$ ${pedidos.desconto}</p>
                        <button type="button" class="btn btn-primary" onclick="finalizar(${pedidos.pedido_id})">Finalizar Pedido</button>
                    </div>
                `;
            });
        })
        .catch(error => {
            console.error(error);
        });

    /////////////// Carrega os produtos para o Modal
    fetch('./run/select-produtos.php')
        .then(response => {
            if (!response.ok) throw new Error('Erro ao buscar os produtos');
            return response.json();
        })
        .then(data => {
            const itens = document.getElementById('form-cadastro-produtos');
            itens.innerHTML = '';
            //////// Carrega e deixa os produtos prontos para serem editados
            data.forEach(produto => {
                const row = document.createElement('div');
                row.classList.add('mb-3');
                row.innerHTML = `
                    <div class="card">
                        <div class="card-body">
                            <input type="hidden" name="produto_id" value="${produto.produto_id}">
                            <div class="mb-2">
                                <label>Nome:</label>
                                <input type="text" class="form-control" id="nome-${produto.produto_id}" value="${produto.produto_name}">
                            </div>
                            <div class="mb-2">
                                <label>Valor:</label>
                                <input type="number" class="form-control" id="valor-${produto.produto_id}" value="${produto.valor}">
                            </div>
                            <div class="mb-2">
                                <label>Estoque:</label>
                                <input type="number" class="form-control" id="estoque-${produto.produto_id}" value="${produto.quantidade ?? 0}">
                            </div>
                            <button class="btn btn-success" onclick="atualizarProduto(${produto.produto_id})">Salvar</button>
                            <button class="btn btn-danger" onclick="deleteProduto(${produto.produto_id})">Delete</button>
                        </div>
                    </div>
                `;
                itens.appendChild(row);
            });
        })
        .catch(error => {
            console.error(error);
        });
});
///// Função para atualizar os Produtos individualmente
function atualizarProduto(id) {
    const nome = document.getElementById(`nome-${id}`).value;
    const valor = document.getElementById(`valor-${id}`).value;
    const estoque = document.getElementById(`estoque-${id}`).value;

    fetch('./run/update-produto.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id, nome, valor, estoque })
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message);
    })
    .catch(err => {
        console.error('Erro ao atualizar:', err);
    });
}
////// Função para deletar um produto caso precise
function deleteProduto(id) {
    if (!confirm("Tem certeza que deseja excluir este produto?")) return;

    fetch('./run/delete-produto.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id })
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message || data.error);
        location.reload(); // Recarrega a página após exclusão
    })
    .catch(err => {
        console.error("Erro ao excluir produto:", err);
        alert("Erro ao excluir o produto.");
    });
}
/////// Função para cadastrar um produto novo do zero
function cadastrarProduto(){
    const form = document.getElementById('cadastro-produto');
    form.innerHTML = '';
    form.innerHTML = `
        <div class="modal-header">
            <h1 class="modal-title fs-5" id="exampleModalLabel">Cadastro de Produtos</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body" id="form-cadastro-produtos">            
            <div class="mb-2">
                <label>Nome:</label>
                <input type="text" class="form-control" id="nome">
            </div>
            <div class="mb-2">
                <label>Valor:</label>
                <input type="number" class="form-control" id="valor">
            </div>
            <div class="mb-2">
                <label>Estoque:</label>
                <input type="number" class="form-control" id="estoque">
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" onclick="cadastrarNew()">Cadastrar novo</button>
        </div>    
    `
}
/////// Função para encviar cadastro novo de produtos
function cadastrarNew(event) {
    const nome = document.getElementById('nome').value;
    const valor = parseFloat(document.getElementById('valor').value);
    const estoque = parseInt(document.getElementById('estoque').value);

    fetch('./run/insert-produto.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ nome, valor, estoque })
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message || data.error);
        location.reload(); 
    })
    .catch(err => {
        console.error('Erro ao cadastrar produto:', err);
    });
}
////// Função para começar a colocar itens no Carrinho de compras
function abrirCarrinho() {
    fetch('./run/select-produtos.php')
    .then(res => res.json())
    .then(data => {
        const carrinho = document.getElementById('carrinho-compra');
        carrinho.innerHTML = '';
        carrinho.innerHTML = `<p>Cupon:</p> <input type="text" class="form-control mb-2" id="cupon">`;
        data.forEach(produto => {
            carrinho.innerHTML += `
                <div class="card mb-2">
                    <div class="card-body">
                        <h5>${produto.produto_name}</h5>
                        <p>Valor: R$ ${produto.valor}</p>
                        <p>Estoque: ${produto.quantidade}</p>
                        <input type="number" class="form-control mb-2" id="qtd-${produto.produto_id}" min="1" max="${produto.quantidade}" value="1">
                        <button class="btn btn-success" onclick="addCarrinho(${produto.produto_id})">Adicionar ao carrinho</button>
                    </div>
                </div>
            `;
        });
    });
}
////// Função que atualiza o carrinho a cada produto adicionado
function addCarrinho(produto_id) {
    const quantidade = parseInt(document.getElementById(`qtd-${produto_id}`).value);

    fetch('./run/adicionar-carrinho.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ produto_id, quantidade })
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message || data.error);
        mostrarCarrinhoAtual();
    });
}
////// Função aque atualiza o valor do carrinho a cada item adcionado
function mostrarCarrinhoAtual() {
    fetch('./run/ver-carrinho.php')
    .then(res => res.json())
    .then(data => {
        const carrinho = document.getElementById('carrinho-compra');
        carrinho.innerHTML += `<hr><h5>Carrinho Atual:</h5>`;
        
        let total = 0;
        data.forEach(item => {
            total += item.quantidade * item.valor;
            carrinho.innerHTML += `
                <p>${item.nome} x${item.quantidade} - R$ ${(item.quantidade * item.valor).toFixed(2)}</p>
            `;
        });

        // Calcular frete baseado no total
        let frete = 0;
        if (total >= 52 && total <= 166.59) {
            frete = 15.00;
        } else if (total > 200) {
            frete = 0.00;
        } else {
            frete = 20.00;
        }

        // Campo de CEP (caso ainda não tenha sido inserido)
        if (!document.getElementById('cep-input')) {
            carrinho.innerHTML += `
                <div class="mb-2">
                    <label>CEP:</label>
                    <input type="text" id="cep" onblur="consultarCEP()">
                    <p id="endereco"></p>
                </div>                
                <div id="frete-info"></div>             
            `;
        }

        carrinho.innerHTML += `<strong id="total-sem-frete">Total (sem frete): R$ ${total.toFixed(2)}</strong><br>`;
        carrinho.innerHTML += `<strong id="frete-valor">Frete: R$ ${frete.toFixed(2)}</strong><br>`;
        carrinho.innerHTML += `<strong id="total-com-frete">Total com frete: R$ ${(total + frete).toFixed(2)}</strong>`;
    });
}
///// Função para testar se o CEP é valido e mandar o CEP junto com o Cupon(se tiver), para a finalização
function comprar() {
    const cep = document.getElementById('cep').value;

    if (!cep || cep.length < 8) {
        alert("Por favor, insira um CEP válido antes de finalizar a compra.");
        return;
    }

    
    const cupon = document.getElementById('cupon').value;


    fetch('./run/finalizar-compra.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ cep, cupon })
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message || data.error);
        location.reload();
    });
}
///// Função para fazer a pesquisa no viacep
function consultarCEP() {
    const cep = document.getElementById('cep').value.replace(/\D/g, '');
    fetch(`https://viacep.com.br/ws/${cep}/json/`)
        .then(res => res.json())
        .then(data => {
            if (data.erro) {
                alert('CEP inválido!');
            } else {
                document.getElementById('endereco').innerText = `${data.logradouro}, ${data.bairro}, ${data.localidade} - ${data.uf}`;
            }
        });
}
////// Função para finalizar um pedido
function finalizar(id) {
    if (!id) {
        alert('ID inválido!');
        return;
    }

    fetch('./run/update-pedido.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id })
    })
    .then(res => res.json())
    .then(data => {
        if (data.error) {
            alert('Erro: ' + data.error);
        } else {
            alert(data.message);
            location.reload();
        }
    })
    .catch(error => {
        console.error('Erro na requisição:', error);
        alert('Erro ao finalizar o pedido. Tente novamente.');
    });
}
</script>
</body>
</html>