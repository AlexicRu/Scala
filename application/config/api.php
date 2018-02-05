<?php defined('SYSPATH') or die('No direct script access');

return ['api' => '{
  "swagger": "2.0",
  "info": {
    "version": "0.1.1",
    "title": "GloPro API"
  },
  "host": "",
  "basePath": "/api",
  "schemes": [
    "https"
  ],
  "consumes": [
    "application/json"
  ],
  "paths": {
    "/login": {
      "post": {
        "tags": ["main"],
        "summary": "Авторизация",
        "operationId": "login",
        "consumes": [
          "application/x-www-form-urlencoded"
        ],
        "parameters": [
          {
            "name": "login",
            "in": "formData",
            "type": "string",
            "required": true
          },
          {
            "name": "password",
            "in": "formData",
            "type": "string",
            "required": true
          }
        ],
        "responses": {
          "200": {
            "description": "Результат",
            "schema": {
              "type": "object",
              "required": [
                "success",
                "data"
              ],
              "properties": {
                "success": {
                  "type": "boolean",
                  "default": true
                },
                "data": {
                  "type": "object",
                  "properties": {
                    "token": {
                      "type": "string"
                    }
                  }
                }
              }
            }
          },
          "400": {
            "description": "Ошибка",
            "schema": {
              "$ref": "#/definitions/ApiBadResponse"
            }
          }
        }
      }
    },
    "/clients": {
      "get": {
        "tags": ["clients"],
        "summary": "Получение списка клиентов",
        "operationId": "clients",
        "parameters": [
          {
            "name": "token",
            "in": "header",
            "type": "string",
            "required": true
          }
        ],
        "responses": {
          "200": {
            "description": "Результат",
            "schema": {
              "type": "object",
              "required": [
                "success",
                "data"
              ],
              "properties": {
                "success": {
                  "type": "boolean",
                  "default": true
                },
                "data": {
                  "type": "array",
                  "items": {
                    "$ref": "#/definitions/ClientModel"
                  }
                }
              }
            }
          },
          "400": {
            "description": "Ошибка",
            "schema": {
              "$ref": "#/definitions/ApiBadResponse"
            }
          }
        }
      }
    },
    "/contracts": {
      "get": {
        "tags": ["contracts"],
        "summary": "Получение списка контрактов",
        "operationId": "contracts",
        "parameters": [
          {
            "name": "token",
            "in": "header",
            "type": "string",
            "required": true
          },
          {
            "name": "client_id",
            "in": "query",
            "type": "integer",
            "required": true
          }
        ],
        "responses": {
          "200": {
            "description": "Результат",
            "schema": {
              "type": "object",
              "required": [
                "success",
                "data"
              ],
              "properties": {
                "success": {
                  "type": "boolean",
                  "default": true
                },
                "data": {
                  "type": "array",
                  "items": {
                    "$ref": "#/definitions/ContractModel"
                  }
                }
              }
            }
          },
          "400": {
            "description": "Ошибка",
            "schema": {
              "$ref": "#/definitions/ApiBadResponse"
            }
          }
        }
      }
    },
    "/transactions": {
      "get": {
        "tags": ["contracts"],
        "summary": "Получение списка транзакций",
        "operationId": "transactions",
        "parameters": [
          {
            "name": "token",
            "in": "header",
            "type": "string",
            "required": true
          },
          {
            "name": "contract_id",
            "in": "query",
            "type": "integer",
            "required": true
          },
          {
            "name": "date_from",
            "in": "query",
            "type": "string",
            "description": "Если не передан параметр, то 01.m.Y"
          },
          {
            "name": "date_to",
            "in": "query",
            "type": "string",
            "description": "Если не передан параметр, то d.m.Y"
          }
        ],
        "responses": {
          "200": {
            "description": "Результат",
            "schema": {
              "type": "object",
              "required": [
                "success",
                "data"
              ],
              "properties": {
                "success": {
                  "type": "boolean",
                  "default": true
                },
                "data": {
                  "type": "array",
                  "items": {
                    "$ref": "#/definitions/TransactionModel"
                  }
                }
              }
            }
          },
          "400": {
            "description": "Ошибка",
            "schema": {
              "$ref": "#/definitions/ApiBadResponse"
            }
          }
        }
      }
    },
    "/cards": {
      "get": {
        "tags": ["cards"],
        "summary": "Получение списка карт",
        "operationId": "cards",
        "parameters": [
          {
            "name": "token",
            "in": "header",
            "type": "string",
            "required": true
          },
          {
            "name": "contract_id",
            "in": "query",
            "type": "integer",
            "required": true
          }
        ],
        "responses": {
          "200": {
            "description": "Результат",
            "schema": {
              "type": "object",
              "required": [
                "success",
                "data"
              ],
              "properties": {
                "success": {
                  "type": "boolean",
                  "default": true
                },
                "data": {
                  "type": "array",
                  "items": {
                    "$ref": "#/definitions/CardModel"
                  }
                }
              }
            }
          },
          "400": {
            "description": "Ошибка",
            "schema": {
              "$ref": "#/definitions/ApiBadResponse"
            }
          }
        }
      }
    },
    "/cards/{card_id}": {
      "get": {
        "tags": [
          "cards"
        ],
        "summary": "Получение карты",
        "operationId": "card",
        "parameters": [
          {
            "name": "token",
            "in": "header",
            "type": "string",
            "required": true
          },
          {
            "name": "card_id",
            "in": "path",
            "type": "string",
            "required": true
          },
          {
            "name": "contract_id",
            "in": "query",
            "type": "integer",
            "required": true
          }
        ],
        "responses": {
          "200": {
            "description": "Результат",
            "schema": {
              "type": "object",
              "required": [
                "success",
                "data"
              ],
              "properties": {
                "success": {
                  "type": "boolean",
                  "default": true
                },
                "data": {
                  "$ref": "#/definitions/CardModel"
                }
              }
            }
          },
          "400": {
            "description": "Ошибка",
            "schema": {
              "$ref": "#/definitions/ApiBadResponse"
            }
          }
        }
      }
    },
    "/card_limits": {
      "get": {
        "deprecated": true,
        "tags": [
          "cards"
        ],
        "summary": "Получение лимитов карты",
        "operationId": "card_limits"
      }
    },
    "/card_status": {
      "post": {
        "tags": ["cards"],
        "summary": "Изменение статуса карты",
        "operationId": "card_status",
        "consumes": [
          "application/x-www-form-urlencoded"
        ],
        "parameters": [
          {
            "name": "token",
            "in": "header",
            "type": "string",
            "required": true
          },
          {
            "name": "card_id",
            "in": "formData",
            "type": "string",
            "required": true
          },
          {
            "name": "contract_id",
            "in": "formData",
            "type": "integer",
            "required": true
          },
          {
            "name": "comment",
            "in": "formData",
            "type": "string",
            "description": "Необходимо указывать при блокировке"
          },
          {
            "name": "block",
            "in": "formData",
            "type": "integer",
            "description": "1 / 0. Если параметр не передан, то происходит toggle статуса блокировки"
          }
        ],
        "responses": {
          "200": {
            "description": "Результат",
            "schema": {
              "$ref": "#/definitions/ApiResponse"
            }
          },
          "400": {
            "description": "Ошибка",
            "schema": {
              "$ref": "#/definitions/ApiBadResponse"
            }
          }
        }
      }
    }
  },
  "definitions": {
    "ApiResponse": {
      "type": "object",
      "required": [
        "success",
        "data"
      ],
      "properties": {
        "success": {
          "type": "boolean",
          "default": true
        }
      }
    },
    "ApiBadResponse": {
      "type": "object",
      "required": [
        "success",
        "data"
      ],
      "properties": {
        "success": {
          "type": "boolean",
          "default": false
        },
        "data": {
          "type": "array",
          "items": {
            "type": "string",
            "description": "errors"
          }
        }
      }
    },
    "ClientModel": {
      "type": "object",
      "properties": {
        "CLIENT_ID": {
          "type": "integer"
        },
        "CLIENT_NAME": {
          "type": "string"
        },
        "LONG_NAME": {
          "type": "string"
        },
        "CLIENT_STATE": {
          "type": "integer"
        }
      }
    },
    "ContractModel": {
      "type": "object",
      "properties": {
        "CONTRACT_ID": {
          "type": "integer"
        },
        "CONTRACT_NAME": {
          "type": "string"
        },
        "DATE_BEGIN": {
          "type": "string",
          "description": "d.m.Y"
        },
        "DATE_END": {
          "type": "string",
          "description": "d.m.Y"
        },
        "CURRENCY": {
          "type": "integer",
          "default": 643
        },
        "CONTRACT_STATUS": {
          "type": "integer"
        }
      }
    },
    "TransactionModel": {
      "type": "object",
      "properties": {
        "DATETIME_TRN": {
          "type": "string"
        },
        "CARD_ID": {
          "type": "string"
        },
        "CLIENT_ID": {
          "type": "integer"
        },
        "CONTRACT_ID": {
          "type": "integer"
        },
        "OPERATION_ID": {
          "type": "integer"
        },
        "SUPPLIER_TERMINAL": {
          "type": "integer"
        },
        "SERVICE_ID": {
          "type": "integer"
        },
        "DESCRIPTION": {
          "type": "string"
        },
        "SERVICE_AMOUNT": {
          "type": "number"
        },
        "SERVICE_PRICE": {
          "type": "number"
        },
        "SERVICE_SUMPRICE": {
          "type": "number"
        },
        "TRN_CURRENCY": {
          "type": "integer",
          "default": 643
        },
        "PRICE_DISCOUNT": {
          "type": "number"
        },
        "SUMPRICE_DISCOUNT": {
          "type": "number"
        },
        "POS_ADDRESS": {
          "type": "string"
        },
        "TRN_KEY": {
          "type": "string"
        },
        "TRN_COMMENT": {
          "type": "string"
        }
      }
    },
    "CardModel": {
      "type": "object",
      "properties": {
        "CARD_ID": {
          "type": "string"
        },
        "HOLDER": {
          "type": "string"
        },
        "DATE_HOLDER": {
          "type": "string"
        },
        "CARD_STATUS": {
          "type": "integer"
        },
        "BLOCK_AVAILABLE": {
          "type": "integer"
        },
        "CHANGE_LIMIT_AVAILABLE": {
          "type": "integer"
        },
        "CARD_COMMENT": {
          "type": "string"
        }
      }
    },
    "CardLimitModel": {
      "type": "object",
      "properties": {

      }
    }
  }
}
'];