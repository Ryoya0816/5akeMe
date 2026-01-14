# auto nvm use when entering a directory with .nvmrc
autoload -U add-zsh-hook

load-nvmrc() {
  local node_version
  local nvmrc_path
  nvmrc_path="$(nvm_find_nvmrc)"

  if [ -n "$nvmrc_path" ]; then
    node_version="$(nvm version "$(cat "$nvmrc_path")")"
    if [ "$node_version" = "N/A" ]; then
      nvm install
    elif [ "$(nvm current)" != "$node_version" ]; then
      nvm use
    fi
  fi
}

add-zsh-hook chpwd load-nvmrc
load-nvmrc
