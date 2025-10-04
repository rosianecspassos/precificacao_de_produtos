<div class="container-fluid">
    <div class="container mt-5">

        @php
            // VARIÁVEIS COM OS VALORES DA SESSION
            $nome_produto = session('nome_produto', '');
            
            // Arrays para repopular os campos dinâmicos
            // Usa [''] para garantir que haja pelo menos um campo renderizado inicialmente
            $custos_array = session('custos', ['']); 
            $taxas_array = session('taxas', ['']);
            
            // Valores escalares para o resultado
            $custo_total = session('custo', 0); 
            $taxa_total = session('taxa', 0);
            
            $lucro = session('lucro', 0);
            $valor_produto = session('valor_produto', null);
        @endphp

        <form action="{{ route('calcular') }}" method="post">
            @csrf

            <div class="text-center mb-5">
                <label for="nome_produto" class="form-label">Nome do Produto:</label>
                <input type="text" class="form-control-prod" id="nome_produto" name="nome_produto" value="{{ old('nome_produto', $nome_produto) }}">
            </div>

   <div class="row ms-5">
    <div class="col">
            <div class="mb-4">
               
          
                <label class="form-label d-block">Custos do Produto (R$):</label>
            
                <div id="custos-container-base">
                    @foreach(old('custo', $custos_array) as $index => $custo)
                        <div class="mb-3 campo-custo">
                            <input type="number" step="0.01" class="form-control" name="custo[]" value="{{ $custo }}" required>
                            {{-- Mostra o botão de remover se houver mais de um campo ou se não for o primeiro --}}
                            @if (count(old('custo', $custos_array)) > 1 || $index > 0)
                                <button type="button" class="btn btn-danger btn-sm mt-2" onclick="removerCampo(this)">Remover</button>
                            @endif
                        </div>
                    @endforeach
                </div>
                
                <div id="custos-container"></div>
                
                <button type="button" class="btn btn-secondary mb-3" onclick="adicionarCampoCusto()">Adicionar Custo +</button>
            </div>
                </div>
                <div class="col">
            
            <div class="mb-4">
                <label class="form-label d-block">Taxas Adicionais (%):</label>
            
                <div id="taxas-container-base">
                    @foreach(old('taxa', $taxas_array) as $index => $taxa)
                        <div class="mb-3 campo-taxa">
                            <input type="number" step="0.01" class="form-control" name="taxa[]" value="{{ $taxa }}" required>
                             {{-- Mostra o botão de remover se houver mais de um campo ou se não for o primeiro --}}
                            @if (count(old('taxa', $taxas_array)) > 1 || $index > 0)
                                <button type="button" class="btn btn-danger btn-sm mt-2" onclick="removerCampo(this)">Remover</button>
                            @endif
                        </div>
                    @endforeach
                </div>
                
                <div id="taxas-container"></div> 
            
                <button type="button" class="btn btn-secondary mb-3" onclick="adicionarCampoTaxa()">Adicionar Taxa +</button>
            </div>
</div>
   <div class="col">
            <div class="mb-3">
                <label for="lucro" class="form-label">Lucro Desejado (%):</label>
                <input type="number" step="0.01" class="form-control" id="lucro" name="lucro" value="{{ old('lucro', $lucro) }}" required>
            </div>
</div>
</div>
            <div class="container text-center mb-5">
                <button type="submit" class="btn btn-primary">Calcular Preço de Venda</button>
            </div>
        </form>

        <div class="alert alert-info" role="alert">
            <h4 class="alert-heading">Resultado do Preço de Venda</h4>

            @if($valor_produto !== null && $valor_produto > 0)
                <div class="alert alert-success mt-3">
                    <p>Produto: <strong>{{ $nome_produto }}</strong></p>
                    <p>Custo Total: R$ {{ number_format($custo_total, 2, ',', '.') }}</p>
                    <p>Taxa Total: {{ number_format($taxa_total, 2, ',', '.') }}%</p>
                    <p>Lucro Desejado: {{ number_format($lucro, 2, ',', '.') }}%</p>
                    <hr>
                    <h4>Preço de Venda Sugerido: <span class="badge bg-primary">R$ {{ number_format($valor_produto, 2, ',', '.') }}</span></h4>
                </div>
            @elseif($valor_produto === 0 && ($custo_total > 0 || $taxa_total > 0))
                <p class="text-danger mt-3">Não foi possível calcular. Verifique se a soma do Lucro Desejado e Taxas é menor que 100%.</p>
            @else
                <p class="mt-3">Preencha os campos e clique em **Calcular Preço de Venda**.</p>
            @endif
        </div>
    </div>
</div>