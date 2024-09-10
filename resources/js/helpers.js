// EXAMPLE USAGE
// import { getErrorString } from "@/helpers";

/**
 * Transforms an error object into HTML string
 *
 * @param {String|Array|null} errors The errors to transform
 */
export const getErrorString = errors => {
  if ( typeof errors === 'string' ) return errors;
  if ( Array.isArray(errors) ) return errors.reduce(( joined, val ) => joined + val + "<br/>");
  return Object.entries(errors).reduce(( joined, [key, val] ) => joined + val + "<br/>", '')
}

export const toCurrency = ( amount, currencySymbol = '₦' ) => {
  if ( isNaN(amount) ) return 'Invalid Amount';

  return currencySymbol + Number(amount).toFixed(2).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,")
}

export const to12HrTime = timeString => new Date('1970-01-01T' + timeString + 'Z').toLocaleTimeString('en-US', { timeZone: 'UTC', hour12: true, hour: 'numeric', minute: 'numeric' });

export const filesize = size => {
  const i = Math.floor(Math.log(size) / Math.log(1024));
  return ( ( size / Math.pow(1024, i) ).toFixed(2) * 1 + ' ' + ['B', 'kB', 'MB', 'GB', 'TB'][i] );
}

export const slugify = str =>
  str
    .toLowerCase()
    .trim()
    .replace(/[^\w\s-]/g, '')
    .replace(/[\s_-]+/g, '-')
    .replace(/^-+|-+$/g, '');

export const collapseCharacters = str =>
  str
    .toLowerCase()
    .trim()
    .replace(/[^\w\s-]/g, '')
    .replace(/[\s_-]+/g, '')
    .replace(/^-+|-+$/g, '');

export const getUrlParamsAsObject = query => {

  query = query.substring(query.indexOf('?') + 1);

  var re = /([^&=]+)=?([^&]*)/g;
  var decodeRE = /\+/g;

  var decode = function ( str ) {
    return decodeURIComponent(str.replace(decodeRE, " "));
  };

  var params = {}, e;
  while ( e = re.exec(query) ) {
    var k = decode(e[1]), v = decode(e[2]);
    if ( k.substring(k.length - 2) === '[]' ) {
      k = k.substring(0, k.length - 2);
      ( params[k] || ( params[k] = [] ) ).push(v);
    } else params[k] = v;
  }

  var assign = function ( obj, keyPath, value ) {
    var lastKeyIndex = keyPath.length - 1;
    for ( var i = 0; i < lastKeyIndex; ++i ) {
      var key = keyPath[i];
      if ( !( key in obj ) )
        obj[key] = {}
      obj = obj[key];
    }
    obj[keyPath[lastKeyIndex]] = value;
  }

  for ( var prop in params ) {
    var structure = prop.split('[');
    if ( structure.length > 1 ) {
      var levels = [];
      structure.forEach(function ( item, i ) {
        var key = item.replace(/[?[\]\\ ]/g, '');
        levels.push(key);
      });
      assign(params, levels, params[prop]);
      delete(params[prop]);
    }
  }
  return params;
};

/**
 * @param {Object} obj The object to encode as url parameters
 * @returns {string}
 */
export const getObjectAsUrlParams = (obj) => {
  const params = [];

  for (const key in obj) {
    params.push(`${encodeURIComponent(key)}=${encodeURIComponent(obj[key])}`);
  }

  return params.join('&');
}

export const isNumeric = val => {
  let num = "" + val; //coerce num to be a string
  return !isNaN(num) && !isNaN(parseFloat(num));
}

// Fisher-Yates shuffle
export const shuffle = arr => {
  for ( let i = arr.length - 1; i > 0; i-- ) {
    let j = Math.floor(Math.random() * ( i + 1 ));
    [arr[i], arr[j]] = [arr[j], arr[i]];
  }
  return arr;
}

/** @type Record<string, string> */
export const imgUrls = import.meta.glob('../../Modules/PublicPage/resources/images/src/**/*.{jpg,jpeg,png,gif,svg,avif,webp}', { eager: true, query: { url: true }, import: 'default' });
/** @type Record<string, CallableFunction> */
export const imgObjs = import.meta.glob('../../Modules/PublicPage/resources/images/src/**/*.{jpg,jpeg,png,gif,svg,avif,webp}', { query: { enhanced: true } });

export const getImgModule = (url, params) => import(/* @vite-ignore */ '../../' + url + '?enhanced&' + params);

export const getImgUrl = (key) => imgUrls['../../' + key]
export const  getImgObj = (key) => imgObjs['../../' + key]()

/**
  <img src="{ getImgUrl('Modules/PublicPage/resources/images/src/logo-light.png') }" class="logo-light" alt="logo" />

  {#await getImgObj('Modules/PublicPage/resources/images/src/logo-light.png') }
    <p>loading...</p>
  {:then src}
    <enhanced:img src="{ src.default }" class="logo-light" alt="logo" />
  {/await}

  getImgModule('Modules/PublicPage/resources/images/src/testimonials/thumbs/china-achuebe.jpg', 'w=20&h=20').then((e) => console.log(e));

  {#await getImgModule(t.img_url, 'w=20&h=20')}
    <p>loading...</p>
  {:then src}
    <enhanced:img src="{ src.default }" class="logo-light" alt="logo" />
  {/await}

  <enhanced:img src="@publicpage-assets/images/src/logo/logo-light.png?enhanced" class="logo-light" alt="logo"/>

  <div style="width:100px; height:50px; background-image: url('{ getImgUrl('Modules/PublicPage/resources/images/src/logo-light.png') }')" class="logo-light" />
 */
