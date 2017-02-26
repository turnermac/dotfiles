execute pathogen#infect()
syntax on
filetype plugin indent on

let g:airline_theme='dark'
let g:colorscheme='elflord'
autocmd VimEnter hello hello.c wincmd p

function OpenNERDTree()
	execute ":NERDTree"
endfunction

command -nargs=0 OpenNERDTree :call OpenNERDTree()
nmap <ESC>t :OpenNERDTree<CR>

" powerline symbols
let g:airline_left_sep = ''
let g:airline_left_alt_sep = ''
let g:airline_right_sep = ''
let g:airline_right_alt_sep = ''
