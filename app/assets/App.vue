<template>
  <div class="container border mt-5 pt-3 bg-light">
    <div class="row">
      <div class="col-3">
        <select
            v-model="request.method"
            name="method"
            id="method"
            class="form-select border-info text-info"
        >
          <option v-for="httpMethod in httpMethods" :value="httpMethod">
            {{ httpMethod }}
          </option>
        </select>
      </div>
      <div class="col-6">
        <input
            v-model="request.url"
            type="text"
            name="url"
            class="form-control w-100"
        >
      </div>
      <div class="col-3">
        <button
            class="btn btn-outline-info w-100"
            @click="sendRequest()"
        >
          Send
        </button>
      </div>
    </div>

    <div class="row p-3">
      <div class="container border">
        <div class="row">
          <tabs-nav
            :id="requestTabsConfig.id"
            :items="requestTabsConfig.items"
          />

          <div class="tab-content" :id="`${requestTabsConfig.id}Content`">

            <!-- headers pane -->
            <tabs-pane
              :id="requestTabsConfig.items[0].id"
              :active="requestTabsConfig.items[0].active"
            >
              <div class="row p-2">
                <button
                    class="btn btn-info w-auto"
                    @click="addHeader()"
                >New</button>
              </div>

              <key-value-input-group
                  :items="request.headers"
                  @remove-item="removeHeader"
              />

            </tabs-pane>

            <!-- query params pane -->
            <tabs-pane
                :id="requestTabsConfig.items[1].id"
                :active="requestTabsConfig.items[1].active"
            >
              <div class="row p-2">
                <button
                    class="btn btn-info w-auto"
                    @click="addQueryParam()"
                >New</button>
              </div>
              <key-value-input-group
                  :items="request.queryParams"
                  @remove-item="removeQueryParam"
              />
            </tabs-pane>

            <!-- body pane -->
            <tabs-pane
                :id="requestTabsConfig.items[2].id"
                :active="requestTabsConfig.items[2].active"
            >
              <textarea
                  v-model="request.body.raw"
                  class="form-control"
                  id="raw-body"
                  tabindex="-1"
                  style="height: 100px"></textarea>
            </tabs-pane>
          </div>
        </div>
      </div>
    </div>

    <div class="row p-3" v-show="displayOutput">
      <div class="container border">
        <div class="row mb-2">
          <div class="col-4 text-center">
            <span
                class="badge rounded-pill"
                :class="statusBadgeClass"
            >
              {{ response.status ? response.status.message : 'N/A' }}
            </span>
          </div>
          <div class="col-4 text-center">
            {{ response.httpVersion ? response.httpVersion : 'N/A' }}
          </div>
          <div class="col-4 text-center">
            {{ response.dateTime ? response.dateTime : 'N/A' }}
          </div>
        </div>

        <div class="row">

          <tabs-nav
              :id="responseTabsConfig.id"
              :items="responseTabsConfig.items"
          />
          <div class="tab-content" :id="`${responseTabsConfig.id}Content`">
            <tabs-pane
              :id="responseTabsConfig.items[0].id"
              :active="responseTabsConfig.items[0].active"
            >
              <div class="row" v-for="(value, key) in response.headers">
                <div class="col-4">
                  {{ key }}
                </div>
                <div class="col-8">
                  {{ value }}
                </div>
              </div>
            </tabs-pane>

            <tabs-pane
                :id="responseTabsConfig.items[1].id"
                :active="responseTabsConfig.items[1].active"
            >
              {{ response.content ? response.content : 'N/A' }}
            </tabs-pane>
          </div>
        </div>
      </div>
    </div>

  </div>
</template>

<script>
  import { HTTP_METHODS } from './constants/http'
  import axios from 'axios';

  import TabsNav from "./components/TabsNav";
  import TabsPane from "./components/TabsPane";
  import KeyValueInputGroup from "./components/KeyValueInputGroup";

  const HTTP_SERVICE_ENDPOINT = 'http://localhost:8080/api/http';

  export default {
    name: 'app',

    components: {
      TabsNav,
      TabsPane,
      KeyValueInputGroup
    },

    data() {
      return {
        displayOutput: false,
        httpMethods: [HTTP_METHODS.GET, HTTP_METHODS.POST, HTTP_METHODS.PATCH, HTTP_METHODS.PUT],
        request: {
          url: 'https://community-open-weather-map.p.rapidapi.com/find',
          method: HTTP_METHODS.GET,
          headers: [
            {
              key: 'X-RapidAPI-Host',
              value: 'community-open-weather-map.p.rapidapi.com'
            },
            {
              key: 'X-RapidAPI-Key',
              value: 'c803b5dec3msh20451a93174c9f6p1ec5d9jsn256f29f7c464'
            }
          ],
          queryParams: [
            {
              key: 'q',
              value: 'london'
            }
          ],
          body: {
            raw: '',
          }
        },
        response: {},
        requestTabsConfig: {
          id: 'requestTabs',
          items: [
            {
              id: 'request-headers',
              displayValue: 'Headers',
              active: false
            },
            {
              id: 'query-params',
              displayValue: 'Query parameters',
              active: false
            },
            {
              id: 'request-body',
              displayValue: 'Request Body',
              active: true
            }
          ]
        },
        responseTabsConfig: {
          id: 'responseTabs',
          items: [
            {
              id: 'response-headers',
              displayValue: 'Headers',
              active: false
            },
            {
              id: 'content',
              displayValue: 'Content',
              active: true
            }
          ]
        }
      }
    },

    computed: {
      statusBadgeClass: function() {
        if(!this.response.status) {
          return 'bg-light';
        }

        const group = this.response.status.code.toString()[0];

        switch (group) {
          case '1':
            return 'bg-info';
          case '2':
            return 'bg-success';
          case '3':
            return 'bg-warning';
          case '4':
          case '5':
            return 'bg-danger'
        }
      },
    },

    methods: {
      addHeader: function() {
        this.request.headers.push({
          key: '',
          value: ''
        });
      },

      addQueryParam: function() {
        this.request.queryParams.push({
          key: '',
          value: ''
        });
      },

      removeHeader: function(i) {
        this.request.headers.splice(i, 1);
      },

      removeQueryParam: function(i) {
        this.request.queryParams.splice(i, 1);
      },

      sendRequest: function() {
        this.displayOutput = true

        axios.post(HTTP_SERVICE_ENDPOINT, this.request)
          .then(({ data }) => {
            this.response = data;
          })
          .catch(error => {
            console.log(error)
          })
      },

      transformRequest: function() {
        const headers = this.request.headers.map(header => {
          return { [header.key]: header.value }
        });

        const queryParams = this.request.queryParams.map(queryParam => {
          return { [queryParam.key]: queryParam.value }
        });

        return {
          url: this.request.url,
          method: this.request.method,
          headers,
          queryParams,
          //content: this.request.body.raw
        }
      }
    }
  }
</script>