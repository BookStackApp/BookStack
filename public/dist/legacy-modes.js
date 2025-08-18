// See the "/licenses" URI for full package license details

// node_modules/@codemirror/legacy-modes/mode/clike.js
function Context(indented, column, type2, info, align, prev) {
  this.indented = indented;
  this.column = column;
  this.type = type2;
  this.info = info;
  this.align = align;
  this.prev = prev;
}
function pushContext(state, col, type2, info) {
  var indent2 = state.indented;
  if (state.context && state.context.type == "statement" && type2 != "statement")
    indent2 = state.context.indented;
  return state.context = new Context(indent2, col, type2, info, null, state.context);
}
function popContext(state) {
  var t = state.context.type;
  if (t == ")" || t == "]" || t == "}")
    state.indented = state.context.indented;
  return state.context = state.context.prev;
}
function typeBefore(stream, state, pos) {
  if (state.prevToken == "variable" || state.prevToken == "type") return true;
  if (/\S(?:[^- ]>|[*\]])\s*$|\*$/.test(stream.string.slice(0, pos))) return true;
  if (state.typeAtEndOfLine && stream.column() == stream.indentation()) return true;
}
function isTopScope(context) {
  for (; ; ) {
    if (!context || context.type == "top") return true;
    if (context.type == "}" && context.prev.info != "namespace") return false;
    context = context.prev;
  }
}
function clike(parserConfig) {
  var statementIndentUnit = parserConfig.statementIndentUnit, dontAlignCalls = parserConfig.dontAlignCalls, keywords14 = parserConfig.keywords || {}, types3 = parserConfig.types || {}, builtin = parserConfig.builtin || {}, blockKeywords = parserConfig.blockKeywords || {}, defKeywords = parserConfig.defKeywords || {}, atoms6 = parserConfig.atoms || {}, hooks = parserConfig.hooks || {}, multiLineStrings = parserConfig.multiLineStrings, indentStatements = parserConfig.indentStatements !== false, indentSwitch = parserConfig.indentSwitch !== false, namespaceSeparator = parserConfig.namespaceSeparator, isPunctuationChar = parserConfig.isPunctuationChar || /[\[\]{}\(\),;\:\.]/, numberStart = parserConfig.numberStart || /[\d\.]/, number = parserConfig.number || /^(?:0x[a-f\d]+|0b[01]+|(?:\d+\.?\d*|\.\d+)(?:e[-+]?\d+)?)(u|ll?|l|f)?/i, isOperatorChar4 = parserConfig.isOperatorChar || /[+\-*&%=<>!?|\/]/, isIdentifierChar = parserConfig.isIdentifierChar || /[\w\$_\xa1-\uffff]/, isReservedIdentifier = parserConfig.isReservedIdentifier || false;
  var curPunc4, isDefKeyword;
  function tokenBase13(stream, state) {
    var ch = stream.next();
    if (hooks[ch]) {
      var result = hooks[ch](stream, state);
      if (result !== false) return result;
    }
    if (ch == '"' || ch == "'") {
      state.tokenize = tokenString8(ch);
      return state.tokenize(stream, state);
    }
    if (numberStart.test(ch)) {
      stream.backUp(1);
      if (stream.match(number)) return "number";
      stream.next();
    }
    if (isPunctuationChar.test(ch)) {
      curPunc4 = ch;
      return null;
    }
    if (ch == "/") {
      if (stream.eat("*")) {
        state.tokenize = tokenComment7;
        return tokenComment7(stream, state);
      }
      if (stream.eat("/")) {
        stream.skipToEnd();
        return "comment";
      }
    }
    if (isOperatorChar4.test(ch)) {
      while (!stream.match(/^\/[\/*]/, false) && stream.eat(isOperatorChar4)) {
      }
      return "operator";
    }
    stream.eatWhile(isIdentifierChar);
    if (namespaceSeparator) while (stream.match(namespaceSeparator))
      stream.eatWhile(isIdentifierChar);
    var cur = stream.current();
    if (contains(keywords14, cur)) {
      if (contains(blockKeywords, cur)) curPunc4 = "newstatement";
      if (contains(defKeywords, cur)) isDefKeyword = true;
      return "keyword";
    }
    if (contains(types3, cur)) return "type";
    if (contains(builtin, cur) || isReservedIdentifier && isReservedIdentifier(cur)) {
      if (contains(blockKeywords, cur)) curPunc4 = "newstatement";
      return "builtin";
    }
    if (contains(atoms6, cur)) return "atom";
    return "variable";
  }
  function tokenString8(quote) {
    return function(stream, state) {
      var escaped = false, next, end = false;
      while ((next = stream.next()) != null) {
        if (next == quote && !escaped) {
          end = true;
          break;
        }
        escaped = !escaped && next == "\\";
      }
      if (end || !(escaped || multiLineStrings))
        state.tokenize = null;
      return "string";
    };
  }
  function tokenComment7(stream, state) {
    var maybeEnd = false, ch;
    while (ch = stream.next()) {
      if (ch == "/" && maybeEnd) {
        state.tokenize = null;
        break;
      }
      maybeEnd = ch == "*";
    }
    return "comment";
  }
  function maybeEOL(stream, state) {
    if (parserConfig.typeFirstDefinitions && stream.eol() && isTopScope(state.context))
      state.typeAtEndOfLine = typeBefore(stream, state, stream.pos);
  }
  return {
    name: parserConfig.name,
    startState: function(indentUnit) {
      return {
        tokenize: null,
        context: new Context(-indentUnit, 0, "top", null, false),
        indented: 0,
        startOfLine: true,
        prevToken: null
      };
    },
    token: function(stream, state) {
      var ctx = state.context;
      if (stream.sol()) {
        if (ctx.align == null) ctx.align = false;
        state.indented = stream.indentation();
        state.startOfLine = true;
      }
      if (stream.eatSpace()) {
        maybeEOL(stream, state);
        return null;
      }
      curPunc4 = isDefKeyword = null;
      var style = (state.tokenize || tokenBase13)(stream, state);
      if (style == "comment" || style == "meta") return style;
      if (ctx.align == null) ctx.align = true;
      if (curPunc4 == ";" || curPunc4 == ":" || curPunc4 == "," && stream.match(/^\s*(?:\/\/.*)?$/, false))
        while (state.context.type == "statement") popContext(state);
      else if (curPunc4 == "{") pushContext(state, stream.column(), "}");
      else if (curPunc4 == "[") pushContext(state, stream.column(), "]");
      else if (curPunc4 == "(") pushContext(state, stream.column(), ")");
      else if (curPunc4 == "}") {
        while (ctx.type == "statement") ctx = popContext(state);
        if (ctx.type == "}") ctx = popContext(state);
        while (ctx.type == "statement") ctx = popContext(state);
      } else if (curPunc4 == ctx.type) popContext(state);
      else if (indentStatements && ((ctx.type == "}" || ctx.type == "top") && curPunc4 != ";" || ctx.type == "statement" && curPunc4 == "newstatement")) {
        pushContext(state, stream.column(), "statement", stream.current());
      }
      if (style == "variable" && (state.prevToken == "def" || parserConfig.typeFirstDefinitions && typeBefore(stream, state, stream.start) && isTopScope(state.context) && stream.match(/^\s*\(/, false)))
        style = "def";
      if (hooks.token) {
        var result = hooks.token(stream, state, style);
        if (result !== void 0) style = result;
      }
      if (style == "def" && parserConfig.styleDefs === false) style = "variable";
      state.startOfLine = false;
      state.prevToken = isDefKeyword ? "def" : style || curPunc4;
      maybeEOL(stream, state);
      return style;
    },
    indent: function(state, textAfter, context) {
      if (state.tokenize != tokenBase13 && state.tokenize != null || state.typeAtEndOfLine && isTopScope(state.context))
        return null;
      var ctx = state.context, firstChar = textAfter && textAfter.charAt(0);
      var closing3 = firstChar == ctx.type;
      if (ctx.type == "statement" && firstChar == "}") ctx = ctx.prev;
      if (parserConfig.dontIndentStatements)
        while (ctx.type == "statement" && parserConfig.dontIndentStatements.test(ctx.info))
          ctx = ctx.prev;
      if (hooks.indent) {
        var hook = hooks.indent(state, ctx, textAfter, context.unit);
        if (typeof hook == "number") return hook;
      }
      var switchBlock = ctx.prev && ctx.prev.info == "switch";
      if (parserConfig.allmanIndentation && /[{(]/.test(firstChar)) {
        while (ctx.type != "top" && ctx.type != "}") ctx = ctx.prev;
        return ctx.indented;
      }
      if (ctx.type == "statement")
        return ctx.indented + (firstChar == "{" ? 0 : statementIndentUnit || context.unit);
      if (ctx.align && (!dontAlignCalls || ctx.type != ")"))
        return ctx.column + (closing3 ? 0 : 1);
      if (ctx.type == ")" && !closing3)
        return ctx.indented + (statementIndentUnit || context.unit);
      return ctx.indented + (closing3 ? 0 : context.unit) + (!closing3 && switchBlock && !/^(?:case|default)\b/.test(textAfter) ? context.unit : 0);
    },
    languageData: {
      indentOnInput: indentSwitch ? /^\s*(?:case .*?:|default:|\{\}?|\})$/ : /^\s*[{}]$/,
      commentTokens: { line: "//", block: { open: "/*", close: "*/" } },
      autocomplete: Object.keys(keywords14).concat(Object.keys(types3)).concat(Object.keys(builtin)).concat(Object.keys(atoms6)),
      ...parserConfig.languageData
    }
  };
}
function words(str) {
  var obj = {}, words8 = str.split(" ");
  for (var i = 0; i < words8.length; ++i) obj[words8[i]] = true;
  return obj;
}
function contains(words8, word) {
  if (typeof words8 === "function") {
    return words8(word);
  } else {
    return words8.propertyIsEnumerable(word);
  }
}
var cKeywords = "auto if break case register continue return default do sizeof static else struct switch extern typedef union for goto while enum const volatile inline restrict asm fortran";
var cppKeywords = "alignas alignof and and_eq audit axiom bitand bitor catch class compl concept constexpr const_cast decltype delete dynamic_cast explicit export final friend import module mutable namespace new noexcept not not_eq operator or or_eq override private protected public reinterpret_cast requires static_assert static_cast template this thread_local throw try typeid typename using virtual xor xor_eq";
var objCKeywords = "bycopy byref in inout oneway out self super atomic nonatomic retain copy readwrite readonly strong weak assign typeof nullable nonnull null_resettable _cmd @interface @implementation @end @protocol @encode @property @synthesize @dynamic @class @public @package @private @protected @required @optional @try @catch @finally @import @selector @encode @defs @synchronized @autoreleasepool @compatibility_alias @available";
var objCBuiltins = "FOUNDATION_EXPORT FOUNDATION_EXTERN NS_INLINE NS_FORMAT_FUNCTION  NS_RETURNS_RETAINEDNS_ERROR_ENUM NS_RETURNS_NOT_RETAINED NS_RETURNS_INNER_POINTER NS_DESIGNATED_INITIALIZER NS_ENUM NS_OPTIONS NS_REQUIRES_NIL_TERMINATION NS_ASSUME_NONNULL_BEGIN NS_ASSUME_NONNULL_END NS_SWIFT_NAME NS_REFINED_FOR_SWIFT";
var basicCTypes = words("int long char short double float unsigned signed void bool");
var basicObjCTypes = words("SEL instancetype id Class Protocol BOOL");
function cTypes(identifier2) {
  return contains(basicCTypes, identifier2) || /.+_t$/.test(identifier2);
}
function objCTypes(identifier2) {
  return cTypes(identifier2) || contains(basicObjCTypes, identifier2);
}
var cBlockKeywords = "case do else for if switch while struct enum union";
var cDefKeywords = "struct enum union";
function cppHook(stream, state) {
  if (!state.startOfLine) return false;
  for (var ch, next = null; ch = stream.peek(); ) {
    if (ch == "\\" && stream.match(/^.$/)) {
      next = cppHook;
      break;
    } else if (ch == "/" && stream.match(/^\/[\/\*]/, false)) {
      break;
    }
    stream.next();
  }
  state.tokenize = next;
  return "meta";
}
function pointerHook(_stream, state) {
  if (state.prevToken == "type") return "type";
  return false;
}
function cIsReservedIdentifier(token) {
  if (!token || token.length < 2) return false;
  if (token[0] != "_") return false;
  return token[1] == "_" || token[1] !== token[1].toLowerCase();
}
function cpp14Literal(stream) {
  stream.eatWhile(/[\w\.']/);
  return "number";
}
function cpp11StringHook(stream, state) {
  stream.backUp(1);
  if (stream.match(/^(?:R|u8R|uR|UR|LR)/)) {
    var match = stream.match(/^"([^\s\\()]{0,16})\(/);
    if (!match) {
      return false;
    }
    state.cpp11RawStringDelim = match[1];
    state.tokenize = tokenRawString;
    return tokenRawString(stream, state);
  }
  if (stream.match(/^(?:u8|u|U|L)/)) {
    if (stream.match(
      /^["']/,
      /* eat */
      false
    )) {
      return "string";
    }
    return false;
  }
  stream.next();
  return false;
}
function cppLooksLikeConstructor(word) {
  var lastTwo = /(\w+)::~?(\w+)$/.exec(word);
  return lastTwo && lastTwo[1] == lastTwo[2];
}
function tokenAtString(stream, state) {
  var next;
  while ((next = stream.next()) != null) {
    if (next == '"' && !stream.eat('"')) {
      state.tokenize = null;
      break;
    }
  }
  return "string";
}
function tokenRawString(stream, state) {
  var delim = state.cpp11RawStringDelim.replace(/[^\w\s]/g, "\\$&");
  var match = stream.match(new RegExp(".*?\\)" + delim + '"'));
  if (match)
    state.tokenize = null;
  else
    stream.skipToEnd();
  return "string";
}
var c = clike({
  name: "c",
  keywords: words(cKeywords),
  types: cTypes,
  blockKeywords: words(cBlockKeywords),
  defKeywords: words(cDefKeywords),
  typeFirstDefinitions: true,
  atoms: words("NULL true false"),
  isReservedIdentifier: cIsReservedIdentifier,
  hooks: {
    "#": cppHook,
    "*": pointerHook
  }
});
var cpp = clike({
  name: "cpp",
  keywords: words(cKeywords + " " + cppKeywords),
  types: cTypes,
  blockKeywords: words(cBlockKeywords + " class try catch"),
  defKeywords: words(cDefKeywords + " class namespace"),
  typeFirstDefinitions: true,
  atoms: words("true false NULL nullptr"),
  dontIndentStatements: /^template$/,
  isIdentifierChar: /[\w\$_~\xa1-\uffff]/,
  isReservedIdentifier: cIsReservedIdentifier,
  hooks: {
    "#": cppHook,
    "*": pointerHook,
    "u": cpp11StringHook,
    "U": cpp11StringHook,
    "L": cpp11StringHook,
    "R": cpp11StringHook,
    "0": cpp14Literal,
    "1": cpp14Literal,
    "2": cpp14Literal,
    "3": cpp14Literal,
    "4": cpp14Literal,
    "5": cpp14Literal,
    "6": cpp14Literal,
    "7": cpp14Literal,
    "8": cpp14Literal,
    "9": cpp14Literal,
    token: function(stream, state, style) {
      if (style == "variable" && stream.peek() == "(" && (state.prevToken == ";" || state.prevToken == null || state.prevToken == "}") && cppLooksLikeConstructor(stream.current()))
        return "def";
    }
  },
  namespaceSeparator: "::"
});
var java = clike({
  name: "java",
  keywords: words("abstract assert break case catch class const continue default do else enum extends final finally for goto if implements import instanceof interface native new package private protected public return static strictfp super switch synchronized this throw throws transient try volatile while @interface"),
  types: words("var byte short int long float double boolean char void Boolean Byte Character Double Float Integer Long Number Object Short String StringBuffer StringBuilder Void"),
  blockKeywords: words("catch class do else finally for if switch try while"),
  defKeywords: words("class interface enum @interface"),
  typeFirstDefinitions: true,
  atoms: words("true false null"),
  number: /^(?:0x[a-f\d_]+|0b[01_]+|(?:[\d_]+\.?\d*|\.\d+)(?:e[-+]?[\d_]+)?)(u|ll?|l|f)?/i,
  hooks: {
    "@": function(stream) {
      if (stream.match("interface", false)) return false;
      stream.eatWhile(/[\w\$_]/);
      return "meta";
    },
    '"': function(stream, state) {
      if (!stream.match(/""$/)) return false;
      state.tokenize = tokenTripleString;
      return state.tokenize(stream, state);
    }
  }
});
var csharp = clike({
  name: "csharp",
  keywords: words("abstract as async await base break case catch checked class const continue default delegate do else enum event explicit extern finally fixed for foreach goto if implicit in init interface internal is lock namespace new operator out override params private protected public readonly record ref required return sealed sizeof stackalloc static struct switch this throw try typeof unchecked unsafe using virtual void volatile while add alias ascending descending dynamic from get global group into join let orderby partial remove select set value var yield"),
  types: words("Action Boolean Byte Char DateTime DateTimeOffset Decimal Double Func Guid Int16 Int32 Int64 Object SByte Single String Task TimeSpan UInt16 UInt32 UInt64 bool byte char decimal double short int long object sbyte float string ushort uint ulong"),
  blockKeywords: words("catch class do else finally for foreach if struct switch try while"),
  defKeywords: words("class interface namespace record struct var"),
  typeFirstDefinitions: true,
  atoms: words("true false null"),
  hooks: {
    "@": function(stream, state) {
      if (stream.eat('"')) {
        state.tokenize = tokenAtString;
        return tokenAtString(stream, state);
      }
      stream.eatWhile(/[\w\$_]/);
      return "meta";
    }
  }
});
function tokenTripleString(stream, state) {
  var escaped = false;
  while (!stream.eol()) {
    if (!escaped && stream.match('"""')) {
      state.tokenize = null;
      break;
    }
    escaped = stream.next() == "\\" && !escaped;
  }
  return "string";
}
function tokenNestedComment(depth) {
  return function(stream, state) {
    var ch;
    while (ch = stream.next()) {
      if (ch == "*" && stream.eat("/")) {
        if (depth == 1) {
          state.tokenize = null;
          break;
        } else {
          state.tokenize = tokenNestedComment(depth - 1);
          return state.tokenize(stream, state);
        }
      } else if (ch == "/" && stream.eat("*")) {
        state.tokenize = tokenNestedComment(depth + 1);
        return state.tokenize(stream, state);
      }
    }
    return "comment";
  };
}
var scala = clike({
  name: "scala",
  keywords: words(
    /* scala */
    "abstract case catch class def do else extends final finally for forSome if implicit import lazy match new null object override package private protected return sealed super this throw trait try type val var while with yield _ assert assume require print println printf readLine readBoolean readByte readShort readChar readInt readLong readFloat readDouble"
  ),
  types: words(
    "AnyVal App Application Array BufferedIterator BigDecimal BigInt Char Console Either Enumeration Equiv Error Exception Fractional Function IndexedSeq Int Integral Iterable Iterator List Map Numeric Nil NotNull Option Ordered Ordering PartialFunction PartialOrdering Product Proxy Range Responder Seq Serializable Set Specializable Stream StringBuilder StringContext Symbol Throwable Traversable TraversableOnce Tuple Unit Vector Boolean Byte Character CharSequence Class ClassLoader Cloneable Comparable Compiler Double Exception Float Integer Long Math Number Object Package Pair Process Runtime Runnable SecurityManager Short StackTraceElement StrictMath String StringBuffer System Thread ThreadGroup ThreadLocal Throwable Triple Void"
  ),
  multiLineStrings: true,
  blockKeywords: words("catch class enum do else finally for forSome if match switch try while"),
  defKeywords: words("class enum def object package trait type val var"),
  atoms: words("true false null"),
  indentStatements: false,
  indentSwitch: false,
  isOperatorChar: /[+\-*&%=<>!?|\/#:@]/,
  hooks: {
    "@": function(stream) {
      stream.eatWhile(/[\w\$_]/);
      return "meta";
    },
    '"': function(stream, state) {
      if (!stream.match('""')) return false;
      state.tokenize = tokenTripleString;
      return state.tokenize(stream, state);
    },
    "'": function(stream) {
      if (stream.match(/^(\\[^'\s]+|[^\\'])'/)) return "character";
      stream.eatWhile(/[\w\$_\xa1-\uffff]/);
      return "atom";
    },
    "=": function(stream, state) {
      var cx = state.context;
      if (cx.type == "}" && cx.align && stream.eat(">")) {
        state.context = new Context(cx.indented, cx.column, cx.type, cx.info, null, cx.prev);
        return "operator";
      } else {
        return false;
      }
    },
    "/": function(stream, state) {
      if (!stream.eat("*")) return false;
      state.tokenize = tokenNestedComment(1);
      return state.tokenize(stream, state);
    }
  },
  languageData: {
    closeBrackets: { brackets: ["(", "[", "{", "'", '"', '"""'] }
  }
});
function tokenKotlinString(tripleString) {
  return function(stream, state) {
    var escaped = false, next, end = false;
    while (!stream.eol()) {
      if (!tripleString && !escaped && stream.match('"')) {
        end = true;
        break;
      }
      if (tripleString && stream.match('"""')) {
        end = true;
        break;
      }
      next = stream.next();
      if (!escaped && next == "$" && stream.match("{"))
        stream.skipTo("}");
      escaped = !escaped && next == "\\" && !tripleString;
    }
    if (end || !tripleString)
      state.tokenize = null;
    return "string";
  };
}
var kotlin = clike({
  name: "kotlin",
  keywords: words(
    /*keywords*/
    "package as typealias class interface this super val operator var fun for is in This throw return annotation break continue object if else while do try when !in !is as? file import where by get set abstract enum open inner override private public internal protected catch finally out final vararg reified dynamic companion constructor init sealed field property receiver param sparam lateinit data inline noinline tailrec external annotation crossinline const operator infix suspend actual expect setparam"
  ),
  types: words(
    /* package java.lang */
    "Boolean Byte Character CharSequence Class ClassLoader Cloneable Comparable Compiler Double Exception Float Integer Long Math Number Object Package Pair Process Runtime Runnable SecurityManager Short StackTraceElement StrictMath String StringBuffer System Thread ThreadGroup ThreadLocal Throwable Triple Void Annotation Any BooleanArray ByteArray Char CharArray DeprecationLevel DoubleArray Enum FloatArray Function Int IntArray Lazy LazyThreadSafetyMode LongArray Nothing ShortArray Unit"
  ),
  intendSwitch: false,
  indentStatements: false,
  multiLineStrings: true,
  number: /^(?:0x[a-f\d_]+|0b[01_]+|(?:[\d_]+(\.\d+)?|\.\d+)(?:e[-+]?[\d_]+)?)(u|ll?|l|f)?/i,
  blockKeywords: words("catch class do else finally for if where try while enum"),
  defKeywords: words("class val var object interface fun"),
  atoms: words("true false null this"),
  hooks: {
    "@": function(stream) {
      stream.eatWhile(/[\w\$_]/);
      return "meta";
    },
    "*": function(_stream, state) {
      return state.prevToken == "." ? "variable" : "operator";
    },
    '"': function(stream, state) {
      state.tokenize = tokenKotlinString(stream.match('""'));
      return state.tokenize(stream, state);
    },
    "/": function(stream, state) {
      if (!stream.eat("*")) return false;
      state.tokenize = tokenNestedComment(1);
      return state.tokenize(stream, state);
    },
    indent: function(state, ctx, textAfter, indentUnit) {
      var firstChar = textAfter && textAfter.charAt(0);
      if ((state.prevToken == "}" || state.prevToken == ")") && textAfter == "")
        return state.indented;
      if (state.prevToken == "operator" && textAfter != "}" && state.context.type != "}" || state.prevToken == "variable" && firstChar == "." || (state.prevToken == "}" || state.prevToken == ")") && firstChar == ".")
        return indentUnit * 2 + ctx.indented;
      if (ctx.align && ctx.type == "}")
        return ctx.indented + (state.context.type == (textAfter || "").charAt(0) ? 0 : indentUnit);
    }
  },
  languageData: {
    closeBrackets: { brackets: ["(", "[", "{", "'", '"', '"""'] }
  }
});
var shader = clike({
  name: "shader",
  keywords: words("sampler1D sampler2D sampler3D samplerCube sampler1DShadow sampler2DShadow const attribute uniform varying break continue discard return for while do if else struct in out inout"),
  types: words("float int bool void vec2 vec3 vec4 ivec2 ivec3 ivec4 bvec2 bvec3 bvec4 mat2 mat3 mat4"),
  blockKeywords: words("for while do if else struct"),
  builtin: words("radians degrees sin cos tan asin acos atan pow exp log exp2 sqrt inversesqrt abs sign floor ceil fract mod min max clamp mix step smoothstep length distance dot cross normalize ftransform faceforward reflect refract matrixCompMult lessThan lessThanEqual greaterThan greaterThanEqual equal notEqual any all not texture1D texture1DProj texture1DLod texture1DProjLod texture2D texture2DProj texture2DLod texture2DProjLod texture3D texture3DProj texture3DLod texture3DProjLod textureCube textureCubeLod shadow1D shadow2D shadow1DProj shadow2DProj shadow1DLod shadow2DLod shadow1DProjLod shadow2DProjLod dFdx dFdy fwidth noise1 noise2 noise3 noise4"),
  atoms: words("true false gl_FragColor gl_SecondaryColor gl_Normal gl_Vertex gl_MultiTexCoord0 gl_MultiTexCoord1 gl_MultiTexCoord2 gl_MultiTexCoord3 gl_MultiTexCoord4 gl_MultiTexCoord5 gl_MultiTexCoord6 gl_MultiTexCoord7 gl_FogCoord gl_PointCoord gl_Position gl_PointSize gl_ClipVertex gl_FrontColor gl_BackColor gl_FrontSecondaryColor gl_BackSecondaryColor gl_TexCoord gl_FogFragCoord gl_FragCoord gl_FrontFacing gl_FragData gl_FragDepth gl_ModelViewMatrix gl_ProjectionMatrix gl_ModelViewProjectionMatrix gl_TextureMatrix gl_NormalMatrix gl_ModelViewMatrixInverse gl_ProjectionMatrixInverse gl_ModelViewProjectionMatrixInverse gl_TextureMatrixTranspose gl_ModelViewMatrixInverseTranspose gl_ProjectionMatrixInverseTranspose gl_ModelViewProjectionMatrixInverseTranspose gl_TextureMatrixInverseTranspose gl_NormalScale gl_DepthRange gl_ClipPlane gl_Point gl_FrontMaterial gl_BackMaterial gl_LightSource gl_LightModel gl_FrontLightModelProduct gl_BackLightModelProduct gl_TextureColor gl_EyePlaneS gl_EyePlaneT gl_EyePlaneR gl_EyePlaneQ gl_FogParameters gl_MaxLights gl_MaxClipPlanes gl_MaxTextureUnits gl_MaxTextureCoords gl_MaxVertexAttribs gl_MaxVertexUniformComponents gl_MaxVaryingFloats gl_MaxVertexTextureImageUnits gl_MaxTextureImageUnits gl_MaxFragmentUniformComponents gl_MaxCombineTextureImageUnits gl_MaxDrawBuffers"),
  indentSwitch: false,
  hooks: { "#": cppHook }
});
var nesC = clike({
  name: "nesc",
  keywords: words(cKeywords + " as atomic async call command component components configuration event generic implementation includes interface module new norace nx_struct nx_union post provides signal task uses abstract extends"),
  types: cTypes,
  blockKeywords: words(cBlockKeywords),
  atoms: words("null true false"),
  hooks: { "#": cppHook }
});
var objectiveC = clike({
  name: "objectivec",
  keywords: words(cKeywords + " " + objCKeywords),
  types: objCTypes,
  builtin: words(objCBuiltins),
  blockKeywords: words(cBlockKeywords + " @synthesize @try @catch @finally @autoreleasepool @synchronized"),
  defKeywords: words(cDefKeywords + " @interface @implementation @protocol @class"),
  dontIndentStatements: /^@.*$/,
  typeFirstDefinitions: true,
  atoms: words("YES NO NULL Nil nil true false nullptr"),
  isReservedIdentifier: cIsReservedIdentifier,
  hooks: {
    "#": cppHook,
    "*": pointerHook
  }
});
var objectiveCpp = clike({
  name: "objectivecpp",
  keywords: words(cKeywords + " " + objCKeywords + " " + cppKeywords),
  types: objCTypes,
  builtin: words(objCBuiltins),
  blockKeywords: words(cBlockKeywords + " @synthesize @try @catch @finally @autoreleasepool @synchronized class try catch"),
  defKeywords: words(cDefKeywords + " @interface @implementation @protocol @class class namespace"),
  dontIndentStatements: /^@.*$|^template$/,
  typeFirstDefinitions: true,
  atoms: words("YES NO NULL Nil nil true false nullptr"),
  isReservedIdentifier: cIsReservedIdentifier,
  hooks: {
    "#": cppHook,
    "*": pointerHook,
    "u": cpp11StringHook,
    "U": cpp11StringHook,
    "L": cpp11StringHook,
    "R": cpp11StringHook,
    "0": cpp14Literal,
    "1": cpp14Literal,
    "2": cpp14Literal,
    "3": cpp14Literal,
    "4": cpp14Literal,
    "5": cpp14Literal,
    "6": cpp14Literal,
    "7": cpp14Literal,
    "8": cpp14Literal,
    "9": cpp14Literal,
    token: function(stream, state, style) {
      if (style == "variable" && stream.peek() == "(" && (state.prevToken == ";" || state.prevToken == null || state.prevToken == "}") && cppLooksLikeConstructor(stream.current()))
        return "def";
    }
  },
  namespaceSeparator: "::"
});
var squirrel = clike({
  name: "squirrel",
  keywords: words("base break clone continue const default delete enum extends function in class foreach local resume return this throw typeof yield constructor instanceof static"),
  types: cTypes,
  blockKeywords: words("case catch class else for foreach if switch try while"),
  defKeywords: words("function local class"),
  typeFirstDefinitions: true,
  atoms: words("true false null"),
  hooks: { "#": cppHook }
});
var stringTokenizer = null;
function tokenCeylonString(type2) {
  return function(stream, state) {
    var escaped = false, next, end = false;
    while (!stream.eol()) {
      if (!escaped && stream.match('"') && (type2 == "single" || stream.match('""'))) {
        end = true;
        break;
      }
      if (!escaped && stream.match("``")) {
        stringTokenizer = tokenCeylonString(type2);
        end = true;
        break;
      }
      next = stream.next();
      escaped = type2 == "single" && !escaped && next == "\\";
    }
    if (end)
      state.tokenize = null;
    return "string";
  };
}
var ceylon = clike({
  name: "ceylon",
  keywords: words("abstracts alias assembly assert assign break case catch class continue dynamic else exists extends finally for function given if import in interface is let module new nonempty object of out outer package return satisfies super switch then this throw try value void while"),
  types: function(word) {
    var first = word.charAt(0);
    return first === first.toUpperCase() && first !== first.toLowerCase();
  },
  blockKeywords: words("case catch class dynamic else finally for function if interface module new object switch try while"),
  defKeywords: words("class dynamic function interface module object package value"),
  builtin: words("abstract actual aliased annotation by default deprecated doc final formal late license native optional sealed see serializable shared suppressWarnings tagged throws variable"),
  isPunctuationChar: /[\[\]{}\(\),;\:\.`]/,
  isOperatorChar: /[+\-*&%=<>!?|^~:\/]/,
  numberStart: /[\d#$]/,
  number: /^(?:#[\da-fA-F_]+|\$[01_]+|[\d_]+[kMGTPmunpf]?|[\d_]+\.[\d_]+(?:[eE][-+]?\d+|[kMGTPmunpf]|)|)/i,
  multiLineStrings: true,
  typeFirstDefinitions: true,
  atoms: words("true false null larger smaller equal empty finished"),
  indentSwitch: false,
  styleDefs: false,
  hooks: {
    "@": function(stream) {
      stream.eatWhile(/[\w\$_]/);
      return "meta";
    },
    '"': function(stream, state) {
      state.tokenize = tokenCeylonString(stream.match('""') ? "triple" : "single");
      return state.tokenize(stream, state);
    },
    "`": function(stream, state) {
      if (!stringTokenizer || !stream.match("`")) return false;
      state.tokenize = stringTokenizer;
      stringTokenizer = null;
      return state.tokenize(stream, state);
    },
    "'": function(stream) {
      if (stream.match(/^(\\[^'\s]+|[^\\'])'/)) return "string.special";
      stream.eatWhile(/[\w\$_\xa1-\uffff]/);
      return "atom";
    },
    token: function(_stream, state, style) {
      if ((style == "variable" || style == "type") && state.prevToken == ".") {
        return "variableName.special";
      }
    }
  },
  languageData: {
    closeBrackets: { brackets: ["(", "[", "{", "'", '"', '"""'] }
  }
});
function pushInterpolationStack(state) {
  (state.interpolationStack || (state.interpolationStack = [])).push(state.tokenize);
}
function popInterpolationStack(state) {
  return (state.interpolationStack || (state.interpolationStack = [])).pop();
}
function sizeInterpolationStack(state) {
  return state.interpolationStack ? state.interpolationStack.length : 0;
}
function tokenDartString(quote, stream, state, raw) {
  var tripleQuoted = false;
  if (stream.eat(quote)) {
    if (stream.eat(quote)) tripleQuoted = true;
    else return "string";
  }
  function tokenStringHelper(stream2, state2) {
    var escaped = false;
    while (!stream2.eol()) {
      if (!raw && !escaped && stream2.peek() == "$") {
        pushInterpolationStack(state2);
        state2.tokenize = tokenInterpolation;
        return "string";
      }
      var next = stream2.next();
      if (next == quote && !escaped && (!tripleQuoted || stream2.match(quote + quote))) {
        state2.tokenize = null;
        break;
      }
      escaped = !raw && !escaped && next == "\\";
    }
    return "string";
  }
  state.tokenize = tokenStringHelper;
  return tokenStringHelper(stream, state);
}
function tokenInterpolation(stream, state) {
  stream.eat("$");
  if (stream.eat("{")) {
    state.tokenize = null;
  } else {
    state.tokenize = tokenInterpolationIdentifier;
  }
  return null;
}
function tokenInterpolationIdentifier(stream, state) {
  stream.eatWhile(/[\w_]/);
  state.tokenize = popInterpolationStack(state);
  return "variable";
}
var dart = clike({
  name: "dart",
  keywords: words("this super static final const abstract class extends external factory implements mixin get native set typedef with enum throw rethrow assert break case continue default in return new deferred async await covariant try catch finally do else for if switch while import library export part of show hide is as extension on yield late required sealed base interface when inline"),
  blockKeywords: words("try catch finally do else for if switch while"),
  builtin: words("void bool num int double dynamic var String Null Never"),
  atoms: words("true false null"),
  // clike numbers without the suffixes, and with '_' separators.
  number: /^(?:0x[a-f\d_]+|(?:[\d_]+\.?[\d_]*|\.[\d_]+)(?:e[-+]?[\d_]+)?)/i,
  hooks: {
    "@": function(stream) {
      stream.eatWhile(/[\w\$_\.]/);
      return "meta";
    },
    // custom string handling to deal with triple-quoted strings and string interpolation
    "'": function(stream, state) {
      return tokenDartString("'", stream, state, false);
    },
    '"': function(stream, state) {
      return tokenDartString('"', stream, state, false);
    },
    "r": function(stream, state) {
      var peek = stream.peek();
      if (peek == "'" || peek == '"') {
        return tokenDartString(stream.next(), stream, state, true);
      }
      return false;
    },
    "}": function(_stream, state) {
      if (sizeInterpolationStack(state) > 0) {
        state.tokenize = popInterpolationStack(state);
        return null;
      }
      return false;
    },
    "/": function(stream, state) {
      if (!stream.eat("*")) return false;
      state.tokenize = tokenNestedComment(1);
      return state.tokenize(stream, state);
    },
    token: function(stream, _, style) {
      if (style == "variable") {
        var isUpper = RegExp("^[_$]*[A-Z][a-zA-Z0-9_$]*$", "g");
        if (isUpper.test(stream.current())) {
          return "type";
        }
      }
    }
  }
});

// node_modules/@codemirror/legacy-modes/mode/clojure.js
var atoms = ["false", "nil", "true"];
var specialForms = [
  ".",
  "catch",
  "def",
  "do",
  "if",
  "monitor-enter",
  "monitor-exit",
  "new",
  "quote",
  "recur",
  "set!",
  "throw",
  "try",
  "var"
];
var coreSymbols = [
  "*",
  "*'",
  "*1",
  "*2",
  "*3",
  "*agent*",
  "*allow-unresolved-vars*",
  "*assert*",
  "*clojure-version*",
  "*command-line-args*",
  "*compile-files*",
  "*compile-path*",
  "*compiler-options*",
  "*data-readers*",
  "*default-data-reader-fn*",
  "*e",
  "*err*",
  "*file*",
  "*flush-on-newline*",
  "*fn-loader*",
  "*in*",
  "*math-context*",
  "*ns*",
  "*out*",
  "*print-dup*",
  "*print-length*",
  "*print-level*",
  "*print-meta*",
  "*print-namespace-maps*",
  "*print-readably*",
  "*read-eval*",
  "*reader-resolver*",
  "*source-path*",
  "*suppress-read*",
  "*unchecked-math*",
  "*use-context-classloader*",
  "*verbose-defrecords*",
  "*warn-on-reflection*",
  "+",
  "+'",
  "-",
  "-'",
  "->",
  "->>",
  "->ArrayChunk",
  "->Eduction",
  "->Vec",
  "->VecNode",
  "->VecSeq",
  "-cache-protocol-fn",
  "-reset-methods",
  "..",
  "/",
  "<",
  "<=",
  "=",
  "==",
  ">",
  ">=",
  "EMPTY-NODE",
  "Inst",
  "StackTraceElement->vec",
  "Throwable->map",
  "accessor",
  "aclone",
  "add-classpath",
  "add-watch",
  "agent",
  "agent-error",
  "agent-errors",
  "aget",
  "alength",
  "alias",
  "all-ns",
  "alter",
  "alter-meta!",
  "alter-var-root",
  "amap",
  "ancestors",
  "and",
  "any?",
  "apply",
  "areduce",
  "array-map",
  "as->",
  "aset",
  "aset-boolean",
  "aset-byte",
  "aset-char",
  "aset-double",
  "aset-float",
  "aset-int",
  "aset-long",
  "aset-short",
  "assert",
  "assoc",
  "assoc!",
  "assoc-in",
  "associative?",
  "atom",
  "await",
  "await-for",
  "await1",
  "bases",
  "bean",
  "bigdec",
  "bigint",
  "biginteger",
  "binding",
  "bit-and",
  "bit-and-not",
  "bit-clear",
  "bit-flip",
  "bit-not",
  "bit-or",
  "bit-set",
  "bit-shift-left",
  "bit-shift-right",
  "bit-test",
  "bit-xor",
  "boolean",
  "boolean-array",
  "boolean?",
  "booleans",
  "bound-fn",
  "bound-fn*",
  "bound?",
  "bounded-count",
  "butlast",
  "byte",
  "byte-array",
  "bytes",
  "bytes?",
  "case",
  "cast",
  "cat",
  "char",
  "char-array",
  "char-escape-string",
  "char-name-string",
  "char?",
  "chars",
  "chunk",
  "chunk-append",
  "chunk-buffer",
  "chunk-cons",
  "chunk-first",
  "chunk-next",
  "chunk-rest",
  "chunked-seq?",
  "class",
  "class?",
  "clear-agent-errors",
  "clojure-version",
  "coll?",
  "comment",
  "commute",
  "comp",
  "comparator",
  "compare",
  "compare-and-set!",
  "compile",
  "complement",
  "completing",
  "concat",
  "cond",
  "cond->",
  "cond->>",
  "condp",
  "conj",
  "conj!",
  "cons",
  "constantly",
  "construct-proxy",
  "contains?",
  "count",
  "counted?",
  "create-ns",
  "create-struct",
  "cycle",
  "dec",
  "dec'",
  "decimal?",
  "declare",
  "dedupe",
  "default-data-readers",
  "definline",
  "definterface",
  "defmacro",
  "defmethod",
  "defmulti",
  "defn",
  "defn-",
  "defonce",
  "defprotocol",
  "defrecord",
  "defstruct",
  "deftype",
  "delay",
  "delay?",
  "deliver",
  "denominator",
  "deref",
  "derive",
  "descendants",
  "destructure",
  "disj",
  "disj!",
  "dissoc",
  "dissoc!",
  "distinct",
  "distinct?",
  "doall",
  "dorun",
  "doseq",
  "dosync",
  "dotimes",
  "doto",
  "double",
  "double-array",
  "double?",
  "doubles",
  "drop",
  "drop-last",
  "drop-while",
  "eduction",
  "empty",
  "empty?",
  "ensure",
  "ensure-reduced",
  "enumeration-seq",
  "error-handler",
  "error-mode",
  "eval",
  "even?",
  "every-pred",
  "every?",
  "ex-data",
  "ex-info",
  "extend",
  "extend-protocol",
  "extend-type",
  "extenders",
  "extends?",
  "false?",
  "ffirst",
  "file-seq",
  "filter",
  "filterv",
  "find",
  "find-keyword",
  "find-ns",
  "find-protocol-impl",
  "find-protocol-method",
  "find-var",
  "first",
  "flatten",
  "float",
  "float-array",
  "float?",
  "floats",
  "flush",
  "fn",
  "fn?",
  "fnext",
  "fnil",
  "for",
  "force",
  "format",
  "frequencies",
  "future",
  "future-call",
  "future-cancel",
  "future-cancelled?",
  "future-done?",
  "future?",
  "gen-class",
  "gen-interface",
  "gensym",
  "get",
  "get-in",
  "get-method",
  "get-proxy-class",
  "get-thread-bindings",
  "get-validator",
  "group-by",
  "halt-when",
  "hash",
  "hash-combine",
  "hash-map",
  "hash-ordered-coll",
  "hash-set",
  "hash-unordered-coll",
  "ident?",
  "identical?",
  "identity",
  "if-let",
  "if-not",
  "if-some",
  "ifn?",
  "import",
  "in-ns",
  "inc",
  "inc'",
  "indexed?",
  "init-proxy",
  "inst-ms",
  "inst-ms*",
  "inst?",
  "instance?",
  "int",
  "int-array",
  "int?",
  "integer?",
  "interleave",
  "intern",
  "interpose",
  "into",
  "into-array",
  "ints",
  "io!",
  "isa?",
  "iterate",
  "iterator-seq",
  "juxt",
  "keep",
  "keep-indexed",
  "key",
  "keys",
  "keyword",
  "keyword?",
  "last",
  "lazy-cat",
  "lazy-seq",
  "let",
  "letfn",
  "line-seq",
  "list",
  "list*",
  "list?",
  "load",
  "load-file",
  "load-reader",
  "load-string",
  "loaded-libs",
  "locking",
  "long",
  "long-array",
  "longs",
  "loop",
  "macroexpand",
  "macroexpand-1",
  "make-array",
  "make-hierarchy",
  "map",
  "map-entry?",
  "map-indexed",
  "map?",
  "mapcat",
  "mapv",
  "max",
  "max-key",
  "memfn",
  "memoize",
  "merge",
  "merge-with",
  "meta",
  "method-sig",
  "methods",
  "min",
  "min-key",
  "mix-collection-hash",
  "mod",
  "munge",
  "name",
  "namespace",
  "namespace-munge",
  "nat-int?",
  "neg-int?",
  "neg?",
  "newline",
  "next",
  "nfirst",
  "nil?",
  "nnext",
  "not",
  "not-any?",
  "not-empty",
  "not-every?",
  "not=",
  "ns",
  "ns-aliases",
  "ns-imports",
  "ns-interns",
  "ns-map",
  "ns-name",
  "ns-publics",
  "ns-refers",
  "ns-resolve",
  "ns-unalias",
  "ns-unmap",
  "nth",
  "nthnext",
  "nthrest",
  "num",
  "number?",
  "numerator",
  "object-array",
  "odd?",
  "or",
  "parents",
  "partial",
  "partition",
  "partition-all",
  "partition-by",
  "pcalls",
  "peek",
  "persistent!",
  "pmap",
  "pop",
  "pop!",
  "pop-thread-bindings",
  "pos-int?",
  "pos?",
  "pr",
  "pr-str",
  "prefer-method",
  "prefers",
  "primitives-classnames",
  "print",
  "print-ctor",
  "print-dup",
  "print-method",
  "print-simple",
  "print-str",
  "printf",
  "println",
  "println-str",
  "prn",
  "prn-str",
  "promise",
  "proxy",
  "proxy-call-with-super",
  "proxy-mappings",
  "proxy-name",
  "proxy-super",
  "push-thread-bindings",
  "pvalues",
  "qualified-ident?",
  "qualified-keyword?",
  "qualified-symbol?",
  "quot",
  "rand",
  "rand-int",
  "rand-nth",
  "random-sample",
  "range",
  "ratio?",
  "rational?",
  "rationalize",
  "re-find",
  "re-groups",
  "re-matcher",
  "re-matches",
  "re-pattern",
  "re-seq",
  "read",
  "read-line",
  "read-string",
  "reader-conditional",
  "reader-conditional?",
  "realized?",
  "record?",
  "reduce",
  "reduce-kv",
  "reduced",
  "reduced?",
  "reductions",
  "ref",
  "ref-history-count",
  "ref-max-history",
  "ref-min-history",
  "ref-set",
  "refer",
  "refer-clojure",
  "reify",
  "release-pending-sends",
  "rem",
  "remove",
  "remove-all-methods",
  "remove-method",
  "remove-ns",
  "remove-watch",
  "repeat",
  "repeatedly",
  "replace",
  "replicate",
  "require",
  "reset!",
  "reset-meta!",
  "reset-vals!",
  "resolve",
  "rest",
  "restart-agent",
  "resultset-seq",
  "reverse",
  "reversible?",
  "rseq",
  "rsubseq",
  "run!",
  "satisfies?",
  "second",
  "select-keys",
  "send",
  "send-off",
  "send-via",
  "seq",
  "seq?",
  "seqable?",
  "seque",
  "sequence",
  "sequential?",
  "set",
  "set-agent-send-executor!",
  "set-agent-send-off-executor!",
  "set-error-handler!",
  "set-error-mode!",
  "set-validator!",
  "set?",
  "short",
  "short-array",
  "shorts",
  "shuffle",
  "shutdown-agents",
  "simple-ident?",
  "simple-keyword?",
  "simple-symbol?",
  "slurp",
  "some",
  "some->",
  "some->>",
  "some-fn",
  "some?",
  "sort",
  "sort-by",
  "sorted-map",
  "sorted-map-by",
  "sorted-set",
  "sorted-set-by",
  "sorted?",
  "special-symbol?",
  "spit",
  "split-at",
  "split-with",
  "str",
  "string?",
  "struct",
  "struct-map",
  "subs",
  "subseq",
  "subvec",
  "supers",
  "swap!",
  "swap-vals!",
  "symbol",
  "symbol?",
  "sync",
  "tagged-literal",
  "tagged-literal?",
  "take",
  "take-last",
  "take-nth",
  "take-while",
  "test",
  "the-ns",
  "thread-bound?",
  "time",
  "to-array",
  "to-array-2d",
  "trampoline",
  "transduce",
  "transient",
  "tree-seq",
  "true?",
  "type",
  "unchecked-add",
  "unchecked-add-int",
  "unchecked-byte",
  "unchecked-char",
  "unchecked-dec",
  "unchecked-dec-int",
  "unchecked-divide-int",
  "unchecked-double",
  "unchecked-float",
  "unchecked-inc",
  "unchecked-inc-int",
  "unchecked-int",
  "unchecked-long",
  "unchecked-multiply",
  "unchecked-multiply-int",
  "unchecked-negate",
  "unchecked-negate-int",
  "unchecked-remainder-int",
  "unchecked-short",
  "unchecked-subtract",
  "unchecked-subtract-int",
  "underive",
  "unquote",
  "unquote-splicing",
  "unreduced",
  "unsigned-bit-shift-right",
  "update",
  "update-in",
  "update-proxy",
  "uri?",
  "use",
  "uuid?",
  "val",
  "vals",
  "var-get",
  "var-set",
  "var?",
  "vary-meta",
  "vec",
  "vector",
  "vector-of",
  "vector?",
  "volatile!",
  "volatile?",
  "vreset!",
  "vswap!",
  "when",
  "when-first",
  "when-let",
  "when-not",
  "when-some",
  "while",
  "with-bindings",
  "with-bindings*",
  "with-in-str",
  "with-loading-context",
  "with-local-vars",
  "with-meta",
  "with-open",
  "with-out-str",
  "with-precision",
  "with-redefs",
  "with-redefs-fn",
  "xml-seq",
  "zero?",
  "zipmap"
];
var haveBodyParameter = [
  "->",
  "->>",
  "as->",
  "binding",
  "bound-fn",
  "case",
  "catch",
  "comment",
  "cond",
  "cond->",
  "cond->>",
  "condp",
  "def",
  "definterface",
  "defmethod",
  "defn",
  "defmacro",
  "defprotocol",
  "defrecord",
  "defstruct",
  "deftype",
  "do",
  "doseq",
  "dotimes",
  "doto",
  "extend",
  "extend-protocol",
  "extend-type",
  "fn",
  "for",
  "future",
  "if",
  "if-let",
  "if-not",
  "if-some",
  "let",
  "letfn",
  "locking",
  "loop",
  "ns",
  "proxy",
  "reify",
  "struct-map",
  "some->",
  "some->>",
  "try",
  "when",
  "when-first",
  "when-let",
  "when-not",
  "when-some",
  "while",
  "with-bindings",
  "with-bindings*",
  "with-in-str",
  "with-loading-context",
  "with-local-vars",
  "with-meta",
  "with-open",
  "with-out-str",
  "with-precision",
  "with-redefs",
  "with-redefs-fn"
];
var atom = createLookupMap(atoms);
var specialForm = createLookupMap(specialForms);
var coreSymbol = createLookupMap(coreSymbols);
var hasBodyParameter = createLookupMap(haveBodyParameter);
var delimiter = /^(?:[\\\[\]\s"(),;@^`{}~]|$)/;
var numberLiteral = /^(?:[+\-]?\d+(?:(?:N|(?:[eE][+\-]?\d+))|(?:\.?\d*(?:M|(?:[eE][+\-]?\d+))?)|\/\d+|[xX][0-9a-fA-F]+|r[0-9a-zA-Z]+)?(?=[\\\[\]\s"#'(),;@^`{}~]|$))/;
var characterLiteral = /^(?:\\(?:backspace|formfeed|newline|return|space|tab|o[0-7]{3}|u[0-9A-Fa-f]{4}|x[0-9A-Fa-f]{4}|.)?(?=[\\\[\]\s"(),;@^`{}~]|$))/;
var qualifiedSymbol = /^(?:(?:[^\\\/\[\]\d\s"#'(),;@^`{}~.][^\\\[\]\s"(),;@^`{}~.\/]*(?:\.[^\\\/\[\]\d\s"#'(),;@^`{}~.][^\\\[\]\s"(),;@^`{}~.\/]*)*\/)?(?:\/|[^\\\/\[\]\d\s"#'(),;@^`{}~][^\\\[\]\s"(),;@^`{}~]*)*(?=[\\\[\]\s"(),;@^`{}~]|$))/;
function base(stream, state) {
  if (stream.eatSpace() || stream.eat(",")) return ["space", null];
  if (stream.match(numberLiteral)) return [null, "number"];
  if (stream.match(characterLiteral)) return [null, "string.special"];
  if (stream.eat(/^"/)) return (state.tokenize = inString)(stream, state);
  if (stream.eat(/^[(\[{]/)) return ["open", "bracket"];
  if (stream.eat(/^[)\]}]/)) return ["close", "bracket"];
  if (stream.eat(/^;/)) {
    stream.skipToEnd();
    return ["space", "comment"];
  }
  if (stream.eat(/^[#'@^`~]/)) return [null, "meta"];
  var matches = stream.match(qualifiedSymbol);
  var symbol2 = matches && matches[0];
  if (!symbol2) {
    stream.next();
    stream.eatWhile(function(c2) {
      return !is(c2, delimiter);
    });
    return [null, "error"];
  }
  if (symbol2 === "comment" && state.lastToken === "(")
    return (state.tokenize = inComment)(stream, state);
  if (is(symbol2, atom) || symbol2.charAt(0) === ":") return ["symbol", "atom"];
  if (is(symbol2, specialForm) || is(symbol2, coreSymbol)) return ["symbol", "keyword"];
  if (state.lastToken === "(") return ["symbol", "builtin"];
  return ["symbol", "variable"];
}
function inString(stream, state) {
  var escaped = false, next;
  while (next = stream.next()) {
    if (next === '"' && !escaped) {
      state.tokenize = base;
      break;
    }
    escaped = !escaped && next === "\\";
  }
  return [null, "string"];
}
function inComment(stream, state) {
  var parenthesisCount = 1;
  var next;
  while (next = stream.next()) {
    if (next === ")") parenthesisCount--;
    if (next === "(") parenthesisCount++;
    if (parenthesisCount === 0) {
      stream.backUp(1);
      state.tokenize = base;
      break;
    }
  }
  return ["space", "comment"];
}
function createLookupMap(words8) {
  var obj = {};
  for (var i = 0; i < words8.length; ++i) obj[words8[i]] = true;
  return obj;
}
function is(value, test) {
  if (test instanceof RegExp) return test.test(value);
  if (test instanceof Object) return test.propertyIsEnumerable(value);
}
var clojure = {
  name: "clojure",
  startState: function() {
    return {
      ctx: { prev: null, start: 0, indentTo: 0 },
      lastToken: null,
      tokenize: base
    };
  },
  token: function(stream, state) {
    if (stream.sol() && typeof state.ctx.indentTo !== "number")
      state.ctx.indentTo = state.ctx.start + 1;
    var typeStylePair = state.tokenize(stream, state);
    var type2 = typeStylePair[0];
    var style = typeStylePair[1];
    var current = stream.current();
    if (type2 !== "space") {
      if (state.lastToken === "(" && state.ctx.indentTo === null) {
        if (type2 === "symbol" && is(current, hasBodyParameter))
          state.ctx.indentTo = state.ctx.start + stream.indentUnit;
        else state.ctx.indentTo = "next";
      } else if (state.ctx.indentTo === "next") {
        state.ctx.indentTo = stream.column();
      }
      state.lastToken = current;
    }
    if (type2 === "open")
      state.ctx = { prev: state.ctx, start: stream.column(), indentTo: null };
    else if (type2 === "close") state.ctx = state.ctx.prev || state.ctx;
    return style;
  },
  indent: function(state) {
    var i = state.ctx.indentTo;
    return typeof i === "number" ? i : state.ctx.start + 1;
  },
  languageData: {
    closeBrackets: { brackets: ["(", "[", "{", '"'] },
    commentTokens: { line: ";;" },
    autocomplete: [].concat(atoms, specialForms, coreSymbols)
  }
};

// node_modules/@codemirror/legacy-modes/mode/diff.js
var TOKEN_NAMES = {
  "+": "inserted",
  "-": "deleted",
  "@": "meta"
};
var diff = {
  name: "diff",
  token: function(stream) {
    var tw_pos = stream.string.search(/[\t ]+?$/);
    if (!stream.sol() || tw_pos === 0) {
      stream.skipToEnd();
      return ("error " + (TOKEN_NAMES[stream.string.charAt(0)] || "")).replace(/ $/, "");
    }
    var token_name = TOKEN_NAMES[stream.peek()] || stream.skipToEnd();
    if (tw_pos === -1) {
      stream.skipToEnd();
    } else {
      stream.pos = tw_pos;
    }
    return token_name;
  }
};

// node_modules/@codemirror/legacy-modes/mode/fortran.js
function words2(array) {
  var keys = {};
  for (var i = 0; i < array.length; ++i) {
    keys[array[i]] = true;
  }
  return keys;
}
var keywords = words2([
  "abstract",
  "accept",
  "allocatable",
  "allocate",
  "array",
  "assign",
  "asynchronous",
  "backspace",
  "bind",
  "block",
  "byte",
  "call",
  "case",
  "class",
  "close",
  "common",
  "contains",
  "continue",
  "cycle",
  "data",
  "deallocate",
  "decode",
  "deferred",
  "dimension",
  "do",
  "elemental",
  "else",
  "encode",
  "end",
  "endif",
  "entry",
  "enumerator",
  "equivalence",
  "exit",
  "external",
  "extrinsic",
  "final",
  "forall",
  "format",
  "function",
  "generic",
  "go",
  "goto",
  "if",
  "implicit",
  "import",
  "include",
  "inquire",
  "intent",
  "interface",
  "intrinsic",
  "module",
  "namelist",
  "non_intrinsic",
  "non_overridable",
  "none",
  "nopass",
  "nullify",
  "open",
  "optional",
  "options",
  "parameter",
  "pass",
  "pause",
  "pointer",
  "print",
  "private",
  "program",
  "protected",
  "public",
  "pure",
  "read",
  "recursive",
  "result",
  "return",
  "rewind",
  "save",
  "select",
  "sequence",
  "stop",
  "subroutine",
  "target",
  "then",
  "to",
  "type",
  "use",
  "value",
  "volatile",
  "where",
  "while",
  "write"
]);
var builtins = words2([
  "abort",
  "abs",
  "access",
  "achar",
  "acos",
  "adjustl",
  "adjustr",
  "aimag",
  "aint",
  "alarm",
  "all",
  "allocated",
  "alog",
  "amax",
  "amin",
  "amod",
  "and",
  "anint",
  "any",
  "asin",
  "associated",
  "atan",
  "besj",
  "besjn",
  "besy",
  "besyn",
  "bit_size",
  "btest",
  "cabs",
  "ccos",
  "ceiling",
  "cexp",
  "char",
  "chdir",
  "chmod",
  "clog",
  "cmplx",
  "command_argument_count",
  "complex",
  "conjg",
  "cos",
  "cosh",
  "count",
  "cpu_time",
  "cshift",
  "csin",
  "csqrt",
  "ctime",
  "c_funloc",
  "c_loc",
  "c_associated",
  "c_null_ptr",
  "c_null_funptr",
  "c_f_pointer",
  "c_null_char",
  "c_alert",
  "c_backspace",
  "c_form_feed",
  "c_new_line",
  "c_carriage_return",
  "c_horizontal_tab",
  "c_vertical_tab",
  "dabs",
  "dacos",
  "dasin",
  "datan",
  "date_and_time",
  "dbesj",
  "dbesj",
  "dbesjn",
  "dbesy",
  "dbesy",
  "dbesyn",
  "dble",
  "dcos",
  "dcosh",
  "ddim",
  "derf",
  "derfc",
  "dexp",
  "digits",
  "dim",
  "dint",
  "dlog",
  "dlog",
  "dmax",
  "dmin",
  "dmod",
  "dnint",
  "dot_product",
  "dprod",
  "dsign",
  "dsinh",
  "dsin",
  "dsqrt",
  "dtanh",
  "dtan",
  "dtime",
  "eoshift",
  "epsilon",
  "erf",
  "erfc",
  "etime",
  "exit",
  "exp",
  "exponent",
  "extends_type_of",
  "fdate",
  "fget",
  "fgetc",
  "float",
  "floor",
  "flush",
  "fnum",
  "fputc",
  "fput",
  "fraction",
  "fseek",
  "fstat",
  "ftell",
  "gerror",
  "getarg",
  "get_command",
  "get_command_argument",
  "get_environment_variable",
  "getcwd",
  "getenv",
  "getgid",
  "getlog",
  "getpid",
  "getuid",
  "gmtime",
  "hostnm",
  "huge",
  "iabs",
  "iachar",
  "iand",
  "iargc",
  "ibclr",
  "ibits",
  "ibset",
  "ichar",
  "idate",
  "idim",
  "idint",
  "idnint",
  "ieor",
  "ierrno",
  "ifix",
  "imag",
  "imagpart",
  "index",
  "int",
  "ior",
  "irand",
  "isatty",
  "ishft",
  "ishftc",
  "isign",
  "iso_c_binding",
  "is_iostat_end",
  "is_iostat_eor",
  "itime",
  "kill",
  "kind",
  "lbound",
  "len",
  "len_trim",
  "lge",
  "lgt",
  "link",
  "lle",
  "llt",
  "lnblnk",
  "loc",
  "log",
  "logical",
  "long",
  "lshift",
  "lstat",
  "ltime",
  "matmul",
  "max",
  "maxexponent",
  "maxloc",
  "maxval",
  "mclock",
  "merge",
  "move_alloc",
  "min",
  "minexponent",
  "minloc",
  "minval",
  "mod",
  "modulo",
  "mvbits",
  "nearest",
  "new_line",
  "nint",
  "not",
  "or",
  "pack",
  "perror",
  "precision",
  "present",
  "product",
  "radix",
  "rand",
  "random_number",
  "random_seed",
  "range",
  "real",
  "realpart",
  "rename",
  "repeat",
  "reshape",
  "rrspacing",
  "rshift",
  "same_type_as",
  "scale",
  "scan",
  "second",
  "selected_int_kind",
  "selected_real_kind",
  "set_exponent",
  "shape",
  "short",
  "sign",
  "signal",
  "sinh",
  "sin",
  "sleep",
  "sngl",
  "spacing",
  "spread",
  "sqrt",
  "srand",
  "stat",
  "sum",
  "symlnk",
  "system",
  "system_clock",
  "tan",
  "tanh",
  "time",
  "tiny",
  "transfer",
  "transpose",
  "trim",
  "ttynam",
  "ubound",
  "umask",
  "unlink",
  "unpack",
  "verify",
  "xor",
  "zabs",
  "zcos",
  "zexp",
  "zlog",
  "zsin",
  "zsqrt"
]);
var dataTypes = words2([
  "c_bool",
  "c_char",
  "c_double",
  "c_double_complex",
  "c_float",
  "c_float_complex",
  "c_funptr",
  "c_int",
  "c_int16_t",
  "c_int32_t",
  "c_int64_t",
  "c_int8_t",
  "c_int_fast16_t",
  "c_int_fast32_t",
  "c_int_fast64_t",
  "c_int_fast8_t",
  "c_int_least16_t",
  "c_int_least32_t",
  "c_int_least64_t",
  "c_int_least8_t",
  "c_intmax_t",
  "c_intptr_t",
  "c_long",
  "c_long_double",
  "c_long_double_complex",
  "c_long_long",
  "c_ptr",
  "c_short",
  "c_signed_char",
  "c_size_t",
  "character",
  "complex",
  "double",
  "integer",
  "logical",
  "real"
]);
var isOperatorChar = /[+\-*&=<>\/\:]/;
var litOperator = /^\.(and|or|eq|lt|le|gt|ge|ne|not|eqv|neqv)\./i;
function tokenBase(stream, state) {
  if (stream.match(litOperator)) {
    return "operator";
  }
  var ch = stream.next();
  if (ch == "!") {
    stream.skipToEnd();
    return "comment";
  }
  if (ch == '"' || ch == "'") {
    state.tokenize = tokenString(ch);
    return state.tokenize(stream, state);
  }
  if (/[\[\]\(\),]/.test(ch)) {
    return null;
  }
  if (/\d/.test(ch)) {
    stream.eatWhile(/[\w\.]/);
    return "number";
  }
  if (isOperatorChar.test(ch)) {
    stream.eatWhile(isOperatorChar);
    return "operator";
  }
  stream.eatWhile(/[\w\$_]/);
  var word = stream.current().toLowerCase();
  if (keywords.hasOwnProperty(word)) {
    return "keyword";
  }
  if (builtins.hasOwnProperty(word) || dataTypes.hasOwnProperty(word)) {
    return "builtin";
  }
  return "variable";
}
function tokenString(quote) {
  return function(stream, state) {
    var escaped = false, next, end = false;
    while ((next = stream.next()) != null) {
      if (next == quote && !escaped) {
        end = true;
        break;
      }
      escaped = !escaped && next == "\\";
    }
    if (end || !escaped) state.tokenize = null;
    return "string";
  };
}
var fortran = {
  name: "fortran",
  startState: function() {
    return { tokenize: null };
  },
  token: function(stream, state) {
    if (stream.eatSpace()) return null;
    var style = (state.tokenize || tokenBase)(stream, state);
    if (style == "comment" || style == "meta") return style;
    return style;
  }
};

// node_modules/@codemirror/legacy-modes/mode/go.js
var keywords2 = {
  "break": true,
  "case": true,
  "chan": true,
  "const": true,
  "continue": true,
  "default": true,
  "defer": true,
  "else": true,
  "fallthrough": true,
  "for": true,
  "func": true,
  "go": true,
  "goto": true,
  "if": true,
  "import": true,
  "interface": true,
  "map": true,
  "package": true,
  "range": true,
  "return": true,
  "select": true,
  "struct": true,
  "switch": true,
  "type": true,
  "var": true,
  "bool": true,
  "byte": true,
  "complex64": true,
  "complex128": true,
  "float32": true,
  "float64": true,
  "int8": true,
  "int16": true,
  "int32": true,
  "int64": true,
  "string": true,
  "uint8": true,
  "uint16": true,
  "uint32": true,
  "uint64": true,
  "int": true,
  "uint": true,
  "uintptr": true,
  "error": true,
  "rune": true,
  "any": true,
  "comparable": true
};
var atoms2 = {
  "true": true,
  "false": true,
  "iota": true,
  "nil": true,
  "append": true,
  "cap": true,
  "close": true,
  "complex": true,
  "copy": true,
  "delete": true,
  "imag": true,
  "len": true,
  "make": true,
  "new": true,
  "panic": true,
  "print": true,
  "println": true,
  "real": true,
  "recover": true
};
var isOperatorChar2 = /[+\-*&^%:=<>!|\/]/;
var curPunc;
function tokenBase2(stream, state) {
  var ch = stream.next();
  if (ch == '"' || ch == "'" || ch == "`") {
    state.tokenize = tokenString2(ch);
    return state.tokenize(stream, state);
  }
  if (/[\d\.]/.test(ch)) {
    if (ch == ".") {
      stream.match(/^[0-9]+([eE][\-+]?[0-9]+)?/);
    } else if (ch == "0") {
      stream.match(/^[xX][0-9a-fA-F]+/) || stream.match(/^0[0-7]+/);
    } else {
      stream.match(/^[0-9]*\.?[0-9]*([eE][\-+]?[0-9]+)?/);
    }
    return "number";
  }
  if (/[\[\]{}\(\),;\:\.]/.test(ch)) {
    curPunc = ch;
    return null;
  }
  if (ch == "/") {
    if (stream.eat("*")) {
      state.tokenize = tokenComment;
      return tokenComment(stream, state);
    }
    if (stream.eat("/")) {
      stream.skipToEnd();
      return "comment";
    }
  }
  if (isOperatorChar2.test(ch)) {
    stream.eatWhile(isOperatorChar2);
    return "operator";
  }
  stream.eatWhile(/[\w\$_\xa1-\uffff]/);
  var cur = stream.current();
  if (keywords2.propertyIsEnumerable(cur)) {
    if (cur == "case" || cur == "default") curPunc = "case";
    return "keyword";
  }
  if (atoms2.propertyIsEnumerable(cur)) return "atom";
  return "variable";
}
function tokenString2(quote) {
  return function(stream, state) {
    var escaped = false, next, end = false;
    while ((next = stream.next()) != null) {
      if (next == quote && !escaped) {
        end = true;
        break;
      }
      escaped = !escaped && quote != "`" && next == "\\";
    }
    if (end || !(escaped || quote == "`"))
      state.tokenize = tokenBase2;
    return "string";
  };
}
function tokenComment(stream, state) {
  var maybeEnd = false, ch;
  while (ch = stream.next()) {
    if (ch == "/" && maybeEnd) {
      state.tokenize = tokenBase2;
      break;
    }
    maybeEnd = ch == "*";
  }
  return "comment";
}
function Context2(indented, column, type2, align, prev) {
  this.indented = indented;
  this.column = column;
  this.type = type2;
  this.align = align;
  this.prev = prev;
}
function pushContext2(state, col, type2) {
  return state.context = new Context2(state.indented, col, type2, null, state.context);
}
function popContext2(state) {
  if (!state.context.prev) return;
  var t = state.context.type;
  if (t == ")" || t == "]" || t == "}")
    state.indented = state.context.indented;
  return state.context = state.context.prev;
}
var go = {
  name: "go",
  startState: function(indentUnit) {
    return {
      tokenize: null,
      context: new Context2(-indentUnit, 0, "top", false),
      indented: 0,
      startOfLine: true
    };
  },
  token: function(stream, state) {
    var ctx = state.context;
    if (stream.sol()) {
      if (ctx.align == null) ctx.align = false;
      state.indented = stream.indentation();
      state.startOfLine = true;
      if (ctx.type == "case") ctx.type = "}";
    }
    if (stream.eatSpace()) return null;
    curPunc = null;
    var style = (state.tokenize || tokenBase2)(stream, state);
    if (style == "comment") return style;
    if (ctx.align == null) ctx.align = true;
    if (curPunc == "{") pushContext2(state, stream.column(), "}");
    else if (curPunc == "[") pushContext2(state, stream.column(), "]");
    else if (curPunc == "(") pushContext2(state, stream.column(), ")");
    else if (curPunc == "case") ctx.type = "case";
    else if (curPunc == "}" && ctx.type == "}") popContext2(state);
    else if (curPunc == ctx.type) popContext2(state);
    state.startOfLine = false;
    return style;
  },
  indent: function(state, textAfter, cx) {
    if (state.tokenize != tokenBase2 && state.tokenize != null) return null;
    var ctx = state.context, firstChar = textAfter && textAfter.charAt(0);
    if (ctx.type == "case" && /^(?:case|default)\b/.test(textAfter)) return ctx.indented;
    var closing3 = firstChar == ctx.type;
    if (ctx.align) return ctx.column + (closing3 ? 0 : 1);
    else return ctx.indented + (closing3 ? 0 : cx.unit);
  },
  languageData: {
    indentOnInput: /^\s([{}]|case |default\s*:)$/,
    commentTokens: { line: "//", block: { open: "/*", close: "*/" } }
  }
};

// node_modules/@codemirror/legacy-modes/mode/haskell.js
function switchState(source, setState, f) {
  setState(f);
  return f(source, setState);
}
var smallRE = /[a-z_]/;
var largeRE = /[A-Z]/;
var digitRE = /\d/;
var hexitRE = /[0-9A-Fa-f]/;
var octitRE = /[0-7]/;
var idRE = /[a-z_A-Z0-9'\xa1-\uffff]/;
var symbolRE = /[-!#$%&*+.\/<=>?@\\^|~:]/;
var specialRE = /[(),;[\]`{}]/;
var whiteCharRE = /[ \t\v\f]/;
function normal(source, setState) {
  if (source.eatWhile(whiteCharRE)) {
    return null;
  }
  var ch = source.next();
  if (specialRE.test(ch)) {
    if (ch == "{" && source.eat("-")) {
      var t = "comment";
      if (source.eat("#")) {
        t = "meta";
      }
      return switchState(source, setState, ncomment(t, 1));
    }
    return null;
  }
  if (ch == "'") {
    if (source.eat("\\")) {
      source.next();
    } else {
      source.next();
    }
    if (source.eat("'")) {
      return "string";
    }
    return "error";
  }
  if (ch == '"') {
    return switchState(source, setState, stringLiteral);
  }
  if (largeRE.test(ch)) {
    source.eatWhile(idRE);
    if (source.eat(".")) {
      return "qualifier";
    }
    return "type";
  }
  if (smallRE.test(ch)) {
    source.eatWhile(idRE);
    return "variable";
  }
  if (digitRE.test(ch)) {
    if (ch == "0") {
      if (source.eat(/[xX]/)) {
        source.eatWhile(hexitRE);
        return "integer";
      }
      if (source.eat(/[oO]/)) {
        source.eatWhile(octitRE);
        return "number";
      }
    }
    source.eatWhile(digitRE);
    var t = "number";
    if (source.match(/^\.\d+/)) {
      t = "number";
    }
    if (source.eat(/[eE]/)) {
      t = "number";
      source.eat(/[-+]/);
      source.eatWhile(digitRE);
    }
    return t;
  }
  if (ch == "." && source.eat("."))
    return "keyword";
  if (symbolRE.test(ch)) {
    if (ch == "-" && source.eat(/-/)) {
      source.eatWhile(/-/);
      if (!source.eat(symbolRE)) {
        source.skipToEnd();
        return "comment";
      }
    }
    source.eatWhile(symbolRE);
    return "variable";
  }
  return "error";
}
function ncomment(type2, nest) {
  if (nest == 0) {
    return normal;
  }
  return function(source, setState) {
    var currNest = nest;
    while (!source.eol()) {
      var ch = source.next();
      if (ch == "{" && source.eat("-")) {
        ++currNest;
      } else if (ch == "-" && source.eat("}")) {
        --currNest;
        if (currNest == 0) {
          setState(normal);
          return type2;
        }
      }
    }
    setState(ncomment(type2, currNest));
    return type2;
  };
}
function stringLiteral(source, setState) {
  while (!source.eol()) {
    var ch = source.next();
    if (ch == '"') {
      setState(normal);
      return "string";
    }
    if (ch == "\\") {
      if (source.eol() || source.eat(whiteCharRE)) {
        setState(stringGap);
        return "string";
      }
      if (source.eat("&")) {
      } else {
        source.next();
      }
    }
  }
  setState(normal);
  return "error";
}
function stringGap(source, setState) {
  if (source.eat("\\")) {
    return switchState(source, setState, stringLiteral);
  }
  source.next();
  setState(normal);
  return "error";
}
var wellKnownWords = function() {
  var wkw = {};
  function setType(t) {
    return function() {
      for (var i = 0; i < arguments.length; i++)
        wkw[arguments[i]] = t;
    };
  }
  setType("keyword")(
    "case",
    "class",
    "data",
    "default",
    "deriving",
    "do",
    "else",
    "foreign",
    "if",
    "import",
    "in",
    "infix",
    "infixl",
    "infixr",
    "instance",
    "let",
    "module",
    "newtype",
    "of",
    "then",
    "type",
    "where",
    "_"
  );
  setType("keyword")(
    "..",
    ":",
    "::",
    "=",
    "\\",
    "<-",
    "->",
    "@",
    "~",
    "=>"
  );
  setType("builtin")(
    "!!",
    "$!",
    "$",
    "&&",
    "+",
    "++",
    "-",
    ".",
    "/",
    "/=",
    "<",
    "<*",
    "<=",
    "<$>",
    "<*>",
    "=<<",
    "==",
    ">",
    ">=",
    ">>",
    ">>=",
    "^",
    "^^",
    "||",
    "*",
    "*>",
    "**"
  );
  setType("builtin")(
    "Applicative",
    "Bool",
    "Bounded",
    "Char",
    "Double",
    "EQ",
    "Either",
    "Enum",
    "Eq",
    "False",
    "FilePath",
    "Float",
    "Floating",
    "Fractional",
    "Functor",
    "GT",
    "IO",
    "IOError",
    "Int",
    "Integer",
    "Integral",
    "Just",
    "LT",
    "Left",
    "Maybe",
    "Monad",
    "Nothing",
    "Num",
    "Ord",
    "Ordering",
    "Rational",
    "Read",
    "ReadS",
    "Real",
    "RealFloat",
    "RealFrac",
    "Right",
    "Show",
    "ShowS",
    "String",
    "True"
  );
  setType("builtin")(
    "abs",
    "acos",
    "acosh",
    "all",
    "and",
    "any",
    "appendFile",
    "asTypeOf",
    "asin",
    "asinh",
    "atan",
    "atan2",
    "atanh",
    "break",
    "catch",
    "ceiling",
    "compare",
    "concat",
    "concatMap",
    "const",
    "cos",
    "cosh",
    "curry",
    "cycle",
    "decodeFloat",
    "div",
    "divMod",
    "drop",
    "dropWhile",
    "either",
    "elem",
    "encodeFloat",
    "enumFrom",
    "enumFromThen",
    "enumFromThenTo",
    "enumFromTo",
    "error",
    "even",
    "exp",
    "exponent",
    "fail",
    "filter",
    "flip",
    "floatDigits",
    "floatRadix",
    "floatRange",
    "floor",
    "fmap",
    "foldl",
    "foldl1",
    "foldr",
    "foldr1",
    "fromEnum",
    "fromInteger",
    "fromIntegral",
    "fromRational",
    "fst",
    "gcd",
    "getChar",
    "getContents",
    "getLine",
    "head",
    "id",
    "init",
    "interact",
    "ioError",
    "isDenormalized",
    "isIEEE",
    "isInfinite",
    "isNaN",
    "isNegativeZero",
    "iterate",
    "last",
    "lcm",
    "length",
    "lex",
    "lines",
    "log",
    "logBase",
    "lookup",
    "map",
    "mapM",
    "mapM_",
    "max",
    "maxBound",
    "maximum",
    "maybe",
    "min",
    "minBound",
    "minimum",
    "mod",
    "negate",
    "not",
    "notElem",
    "null",
    "odd",
    "or",
    "otherwise",
    "pi",
    "pred",
    "print",
    "product",
    "properFraction",
    "pure",
    "putChar",
    "putStr",
    "putStrLn",
    "quot",
    "quotRem",
    "read",
    "readFile",
    "readIO",
    "readList",
    "readLn",
    "readParen",
    "reads",
    "readsPrec",
    "realToFrac",
    "recip",
    "rem",
    "repeat",
    "replicate",
    "return",
    "reverse",
    "round",
    "scaleFloat",
    "scanl",
    "scanl1",
    "scanr",
    "scanr1",
    "seq",
    "sequence",
    "sequence_",
    "show",
    "showChar",
    "showList",
    "showParen",
    "showString",
    "shows",
    "showsPrec",
    "significand",
    "signum",
    "sin",
    "sinh",
    "snd",
    "span",
    "splitAt",
    "sqrt",
    "subtract",
    "succ",
    "sum",
    "tail",
    "take",
    "takeWhile",
    "tan",
    "tanh",
    "toEnum",
    "toInteger",
    "toRational",
    "truncate",
    "uncurry",
    "undefined",
    "unlines",
    "until",
    "unwords",
    "unzip",
    "unzip3",
    "userError",
    "words",
    "writeFile",
    "zip",
    "zip3",
    "zipWith",
    "zipWith3"
  );
  return wkw;
}();
var haskell = {
  name: "haskell",
  startState: function() {
    return { f: normal };
  },
  copyState: function(s) {
    return { f: s.f };
  },
  token: function(stream, state) {
    var t = state.f(stream, function(s) {
      state.f = s;
    });
    var w = stream.current();
    return wellKnownWords.hasOwnProperty(w) ? wellKnownWords[w] : t;
  },
  languageData: {
    commentTokens: { line: "--", block: { open: "{-", close: "-}" } }
  }
};

// node_modules/@codemirror/legacy-modes/mode/julia.js
function wordRegexp(words8, end, pre) {
  if (typeof pre === "undefined") pre = "";
  if (typeof end === "undefined") {
    end = "\\b";
  }
  return new RegExp("^" + pre + "((" + words8.join(")|(") + "))" + end);
}
var octChar = "\\\\[0-7]{1,3}";
var hexChar = "\\\\x[A-Fa-f0-9]{1,2}";
var sChar = `\\\\[abefnrtv0%?'"\\\\]`;
var uChar = "([^\\u0027\\u005C\\uD800-\\uDFFF]|[\\uD800-\\uDFFF][\\uDC00-\\uDFFF])";
var asciiOperatorsList = [
  "[<>]:",
  "[<>=]=",
  "<<=?",
  ">>>?=?",
  "=>",
  "--?>",
  "<--[->]?",
  "\\/\\/",
  "\\.{2,3}",
  "[\\.\\\\%*+\\-<>!\\/^|&]=?",
  "\\?",
  "\\$",
  "~",
  ":"
];
var operators = wordRegexp([
  "[<>]:",
  "[<>=]=",
  "[!=]==",
  "<<=?",
  ">>>?=?",
  "=>?",
  "--?>",
  "<--[->]?",
  "\\/\\/",
  "[\\\\%*+\\-<>!\\/^|&\\u00F7\\u22BB]=?",
  "\\?",
  "\\$",
  "~",
  ":",
  "\\u00D7",
  "\\u2208",
  "\\u2209",
  "\\u220B",
  "\\u220C",
  "\\u2218",
  "\\u221A",
  "\\u221B",
  "\\u2229",
  "\\u222A",
  "\\u2260",
  "\\u2264",
  "\\u2265",
  "\\u2286",
  "\\u2288",
  "\\u228A",
  "\\u22C5",
  "\\b(in|isa)\\b(?!.?\\()"
], "");
var delimiters = /^[;,()[\]{}]/;
var identifiers = /^[_A-Za-z\u00A1-\u2217\u2219-\uFFFF][\w\u00A1-\u2217\u2219-\uFFFF]*!*/;
var chars = wordRegexp([octChar, hexChar, sChar, uChar], "'");
var openersList = [
  "begin",
  "function",
  "type",
  "struct",
  "immutable",
  "let",
  "macro",
  "for",
  "while",
  "quote",
  "if",
  "else",
  "elseif",
  "try",
  "finally",
  "catch",
  "do"
];
var closersList = ["end", "else", "elseif", "catch", "finally"];
var keywordsList = [
  "if",
  "else",
  "elseif",
  "while",
  "for",
  "begin",
  "let",
  "end",
  "do",
  "try",
  "catch",
  "finally",
  "return",
  "break",
  "continue",
  "global",
  "local",
  "const",
  "export",
  "import",
  "importall",
  "using",
  "function",
  "where",
  "macro",
  "module",
  "baremodule",
  "struct",
  "type",
  "mutable",
  "immutable",
  "quote",
  "typealias",
  "abstract",
  "primitive",
  "bitstype"
];
var builtinsList = ["true", "false", "nothing", "NaN", "Inf"];
var openers = wordRegexp(openersList);
var closers = wordRegexp(closersList);
var keywords3 = wordRegexp(keywordsList);
var builtins2 = wordRegexp(builtinsList);
var macro = /^@[_A-Za-z\u00A1-\uFFFF][\w\u00A1-\uFFFF]*!*/;
var symbol = /^:[_A-Za-z\u00A1-\uFFFF][\w\u00A1-\uFFFF]*!*/;
var stringPrefixes = /^(`|([_A-Za-z\u00A1-\uFFFF]*"("")?))/;
var macroOperators = wordRegexp(asciiOperatorsList, "", "@");
var symbolOperators = wordRegexp(asciiOperatorsList, "", ":");
function inArray(state) {
  return state.nestedArrays > 0;
}
function inGenerator(state) {
  return state.nestedGenerators > 0;
}
function currentScope(state, n) {
  if (typeof n === "undefined") {
    n = 0;
  }
  if (state.scopes.length <= n) {
    return null;
  }
  return state.scopes[state.scopes.length - (n + 1)];
}
function tokenBase3(stream, state) {
  if (stream.match("#=", false)) {
    state.tokenize = tokenComment2;
    return state.tokenize(stream, state);
  }
  var leavingExpr = state.leavingExpr;
  if (stream.sol()) {
    leavingExpr = false;
  }
  state.leavingExpr = false;
  if (leavingExpr) {
    if (stream.match(/^'+/)) {
      return "operator";
    }
  }
  if (stream.match(/\.{4,}/)) {
    return "error";
  } else if (stream.match(/\.{1,3}/)) {
    return "operator";
  }
  if (stream.eatSpace()) {
    return null;
  }
  var ch = stream.peek();
  if (ch === "#") {
    stream.skipToEnd();
    return "comment";
  }
  if (ch === "[") {
    state.scopes.push("[");
    state.nestedArrays++;
  }
  if (ch === "(") {
    state.scopes.push("(");
    state.nestedGenerators++;
  }
  if (inArray(state) && ch === "]") {
    while (state.scopes.length && currentScope(state) !== "[") {
      state.scopes.pop();
    }
    state.scopes.pop();
    state.nestedArrays--;
    state.leavingExpr = true;
  }
  if (inGenerator(state) && ch === ")") {
    while (state.scopes.length && currentScope(state) !== "(") {
      state.scopes.pop();
    }
    state.scopes.pop();
    state.nestedGenerators--;
    state.leavingExpr = true;
  }
  if (inArray(state)) {
    if (state.lastToken == "end" && stream.match(":")) {
      return "operator";
    }
    if (stream.match("end")) {
      return "number";
    }
  }
  var match;
  if (match = stream.match(openers, false)) {
    state.scopes.push(match[0]);
  }
  if (stream.match(closers, false)) {
    state.scopes.pop();
  }
  if (stream.match(/^::(?![:\$])/)) {
    state.tokenize = tokenAnnotation;
    return state.tokenize(stream, state);
  }
  if (!leavingExpr && (stream.match(symbol) || stream.match(symbolOperators))) {
    return "builtin";
  }
  if (stream.match(operators)) {
    return "operator";
  }
  if (stream.match(/^\.?\d/, false)) {
    var imMatcher = RegExp(/^im\b/);
    var numberLiteral2 = false;
    if (stream.match(/^0x\.[0-9a-f_]+p[\+\-]?[_\d]+/i)) {
      numberLiteral2 = true;
    }
    if (stream.match(/^0x[0-9a-f_]+/i)) {
      numberLiteral2 = true;
    }
    if (stream.match(/^0b[01_]+/i)) {
      numberLiteral2 = true;
    }
    if (stream.match(/^0o[0-7_]+/i)) {
      numberLiteral2 = true;
    }
    if (stream.match(/^(?:(?:\d[_\d]*)?\.(?!\.)(?:\d[_\d]*)?|\d[_\d]*\.(?!\.)(?:\d[_\d]*))?([Eef][\+\-]?[_\d]+)?/i)) {
      numberLiteral2 = true;
    }
    if (stream.match(/^\d[_\d]*(e[\+\-]?\d+)?/i)) {
      numberLiteral2 = true;
    }
    if (numberLiteral2) {
      stream.match(imMatcher);
      state.leavingExpr = true;
      return "number";
    }
  }
  if (stream.match("'")) {
    state.tokenize = tokenChar;
    return state.tokenize(stream, state);
  }
  if (stream.match(stringPrefixes)) {
    state.tokenize = tokenStringFactory(stream.current());
    return state.tokenize(stream, state);
  }
  if (stream.match(macro) || stream.match(macroOperators)) {
    return "meta";
  }
  if (stream.match(delimiters)) {
    return null;
  }
  if (stream.match(keywords3)) {
    return "keyword";
  }
  if (stream.match(builtins2)) {
    return "builtin";
  }
  var isDefinition = state.isDefinition || state.lastToken == "function" || state.lastToken == "macro" || state.lastToken == "type" || state.lastToken == "struct" || state.lastToken == "immutable";
  if (stream.match(identifiers)) {
    if (isDefinition) {
      if (stream.peek() === ".") {
        state.isDefinition = true;
        return "variable";
      }
      state.isDefinition = false;
      return "def";
    }
    state.leavingExpr = true;
    return "variable";
  }
  stream.next();
  return "error";
}
function tokenAnnotation(stream, state) {
  stream.match(/.*?(?=[,;{}()=\s]|$)/);
  if (stream.match("{")) {
    state.nestedParameters++;
  } else if (stream.match("}") && state.nestedParameters > 0) {
    state.nestedParameters--;
  }
  if (state.nestedParameters > 0) {
    stream.match(/.*?(?={|})/) || stream.next();
  } else if (state.nestedParameters == 0) {
    state.tokenize = tokenBase3;
  }
  return "builtin";
}
function tokenComment2(stream, state) {
  if (stream.match("#=")) {
    state.nestedComments++;
  }
  if (!stream.match(/.*?(?=(#=|=#))/)) {
    stream.skipToEnd();
  }
  if (stream.match("=#")) {
    state.nestedComments--;
    if (state.nestedComments == 0)
      state.tokenize = tokenBase3;
  }
  return "comment";
}
function tokenChar(stream, state) {
  var isChar = false, match;
  if (stream.match(chars)) {
    isChar = true;
  } else if (match = stream.match(/\\u([a-f0-9]{1,4})(?=')/i)) {
    var value = parseInt(match[1], 16);
    if (value <= 55295 || value >= 57344) {
      isChar = true;
      stream.next();
    }
  } else if (match = stream.match(/\\U([A-Fa-f0-9]{5,8})(?=')/)) {
    var value = parseInt(match[1], 16);
    if (value <= 1114111) {
      isChar = true;
      stream.next();
    }
  }
  if (isChar) {
    state.leavingExpr = true;
    state.tokenize = tokenBase3;
    return "string";
  }
  if (!stream.match(/^[^']+(?=')/)) {
    stream.skipToEnd();
  }
  if (stream.match("'")) {
    state.tokenize = tokenBase3;
  }
  return "error";
}
function tokenStringFactory(delimiter2) {
  if (delimiter2.substr(-3) === '"""') {
    delimiter2 = '"""';
  } else if (delimiter2.substr(-1) === '"') {
    delimiter2 = '"';
  }
  function tokenString8(stream, state) {
    if (stream.eat("\\")) {
      stream.next();
    } else if (stream.match(delimiter2)) {
      state.tokenize = tokenBase3;
      state.leavingExpr = true;
      return "string";
    } else {
      stream.eat(/[`"]/);
    }
    stream.eatWhile(/[^\\`"]/);
    return "string";
  }
  return tokenString8;
}
var julia = {
  name: "julia",
  startState: function() {
    return {
      tokenize: tokenBase3,
      scopes: [],
      lastToken: null,
      leavingExpr: false,
      isDefinition: false,
      nestedArrays: 0,
      nestedComments: 0,
      nestedGenerators: 0,
      nestedParameters: 0,
      firstParenPos: -1
    };
  },
  token: function(stream, state) {
    var style = state.tokenize(stream, state);
    var current = stream.current();
    if (current && style) {
      state.lastToken = current;
    }
    return style;
  },
  indent: function(state, textAfter, cx) {
    var delta = 0;
    if (textAfter === "]" || textAfter === ")" || /^end\b/.test(textAfter) || /^else/.test(textAfter) || /^catch\b/.test(textAfter) || /^elseif\b/.test(textAfter) || /^finally/.test(textAfter)) {
      delta = -1;
    }
    return (state.scopes.length + delta) * cx.unit;
  },
  languageData: {
    indentOnInput: /^\s*(end|else|catch|finally)\b$/,
    commentTokens: { line: "#", block: { open: "#=", close: "=#" } },
    closeBrackets: { brackets: ["(", "[", "{", '"'] },
    autocomplete: keywordsList.concat(builtinsList)
  }
};

// node_modules/@codemirror/legacy-modes/mode/lua.js
function prefixRE(words8) {
  return new RegExp("^(?:" + words8.join("|") + ")", "i");
}
function wordRE(words8) {
  return new RegExp("^(?:" + words8.join("|") + ")$", "i");
}
var builtins3 = wordRE([
  "_G",
  "_VERSION",
  "assert",
  "collectgarbage",
  "dofile",
  "error",
  "getfenv",
  "getmetatable",
  "ipairs",
  "load",
  "loadfile",
  "loadstring",
  "module",
  "next",
  "pairs",
  "pcall",
  "print",
  "rawequal",
  "rawget",
  "rawset",
  "require",
  "select",
  "setfenv",
  "setmetatable",
  "tonumber",
  "tostring",
  "type",
  "unpack",
  "xpcall",
  "coroutine.create",
  "coroutine.resume",
  "coroutine.running",
  "coroutine.status",
  "coroutine.wrap",
  "coroutine.yield",
  "debug.debug",
  "debug.getfenv",
  "debug.gethook",
  "debug.getinfo",
  "debug.getlocal",
  "debug.getmetatable",
  "debug.getregistry",
  "debug.getupvalue",
  "debug.setfenv",
  "debug.sethook",
  "debug.setlocal",
  "debug.setmetatable",
  "debug.setupvalue",
  "debug.traceback",
  "close",
  "flush",
  "lines",
  "read",
  "seek",
  "setvbuf",
  "write",
  "io.close",
  "io.flush",
  "io.input",
  "io.lines",
  "io.open",
  "io.output",
  "io.popen",
  "io.read",
  "io.stderr",
  "io.stdin",
  "io.stdout",
  "io.tmpfile",
  "io.type",
  "io.write",
  "math.abs",
  "math.acos",
  "math.asin",
  "math.atan",
  "math.atan2",
  "math.ceil",
  "math.cos",
  "math.cosh",
  "math.deg",
  "math.exp",
  "math.floor",
  "math.fmod",
  "math.frexp",
  "math.huge",
  "math.ldexp",
  "math.log",
  "math.log10",
  "math.max",
  "math.min",
  "math.modf",
  "math.pi",
  "math.pow",
  "math.rad",
  "math.random",
  "math.randomseed",
  "math.sin",
  "math.sinh",
  "math.sqrt",
  "math.tan",
  "math.tanh",
  "os.clock",
  "os.date",
  "os.difftime",
  "os.execute",
  "os.exit",
  "os.getenv",
  "os.remove",
  "os.rename",
  "os.setlocale",
  "os.time",
  "os.tmpname",
  "package.cpath",
  "package.loaded",
  "package.loaders",
  "package.loadlib",
  "package.path",
  "package.preload",
  "package.seeall",
  "string.byte",
  "string.char",
  "string.dump",
  "string.find",
  "string.format",
  "string.gmatch",
  "string.gsub",
  "string.len",
  "string.lower",
  "string.match",
  "string.rep",
  "string.reverse",
  "string.sub",
  "string.upper",
  "table.concat",
  "table.insert",
  "table.maxn",
  "table.remove",
  "table.sort"
]);
var keywords4 = wordRE([
  "and",
  "break",
  "elseif",
  "false",
  "nil",
  "not",
  "or",
  "return",
  "true",
  "function",
  "end",
  "if",
  "then",
  "else",
  "do",
  "while",
  "repeat",
  "until",
  "for",
  "in",
  "local"
]);
var indentTokens = wordRE(["function", "if", "repeat", "do", "\\(", "{"]);
var dedentTokens = wordRE(["end", "until", "\\)", "}"]);
var dedentPartial = prefixRE(["end", "until", "\\)", "}", "else", "elseif"]);
function readBracket(stream) {
  var level = 0;
  while (stream.eat("=")) ++level;
  stream.eat("[");
  return level;
}
function normal2(stream, state) {
  var ch = stream.next();
  if (ch == "-" && stream.eat("-")) {
    if (stream.eat("[") && stream.eat("["))
      return (state.cur = bracketed(readBracket(stream), "comment"))(stream, state);
    stream.skipToEnd();
    return "comment";
  }
  if (ch == '"' || ch == "'")
    return (state.cur = string(ch))(stream, state);
  if (ch == "[" && /[\[=]/.test(stream.peek()))
    return (state.cur = bracketed(readBracket(stream), "string"))(stream, state);
  if (/\d/.test(ch)) {
    stream.eatWhile(/[\w.%]/);
    return "number";
  }
  if (/[\w_]/.test(ch)) {
    stream.eatWhile(/[\w\\\-_.]/);
    return "variable";
  }
  return null;
}
function bracketed(level, style) {
  return function(stream, state) {
    var curlev = null, ch;
    while ((ch = stream.next()) != null) {
      if (curlev == null) {
        if (ch == "]") curlev = 0;
      } else if (ch == "=") ++curlev;
      else if (ch == "]" && curlev == level) {
        state.cur = normal2;
        break;
      } else curlev = null;
    }
    return style;
  };
}
function string(quote) {
  return function(stream, state) {
    var escaped = false, ch;
    while ((ch = stream.next()) != null) {
      if (ch == quote && !escaped) break;
      escaped = !escaped && ch == "\\";
    }
    if (!escaped) state.cur = normal2;
    return "string";
  };
}
var lua = {
  name: "lua",
  startState: function() {
    return { basecol: 0, indentDepth: 0, cur: normal2 };
  },
  token: function(stream, state) {
    if (stream.eatSpace()) return null;
    var style = state.cur(stream, state);
    var word = stream.current();
    if (style == "variable") {
      if (keywords4.test(word)) style = "keyword";
      else if (builtins3.test(word)) style = "builtin";
    }
    if (style != "comment" && style != "string") {
      if (indentTokens.test(word)) ++state.indentDepth;
      else if (dedentTokens.test(word)) --state.indentDepth;
    }
    return style;
  },
  indent: function(state, textAfter, cx) {
    var closing3 = dedentPartial.test(textAfter);
    return state.basecol + cx.unit * (state.indentDepth - (closing3 ? 1 : 0));
  },
  languageData: {
    indentOnInput: /^\s*(?:end|until|else|\)|\})$/,
    commentTokens: { line: "--", block: { open: "--[[", close: "]]--" } }
  }
};

// node_modules/@codemirror/legacy-modes/mode/mllike.js
function mlLike(parserConfig) {
  var words8 = {
    "as": "keyword",
    "do": "keyword",
    "else": "keyword",
    "end": "keyword",
    "exception": "keyword",
    "fun": "keyword",
    "functor": "keyword",
    "if": "keyword",
    "in": "keyword",
    "include": "keyword",
    "let": "keyword",
    "of": "keyword",
    "open": "keyword",
    "rec": "keyword",
    "struct": "keyword",
    "then": "keyword",
    "type": "keyword",
    "val": "keyword",
    "while": "keyword",
    "with": "keyword"
  };
  var extraWords = parserConfig.extraWords || {};
  for (var prop in extraWords) {
    if (extraWords.hasOwnProperty(prop)) {
      words8[prop] = parserConfig.extraWords[prop];
    }
  }
  var hintWords = [];
  for (var k in words8) {
    hintWords.push(k);
  }
  function tokenBase13(stream, state) {
    var ch = stream.next();
    if (ch === '"') {
      state.tokenize = tokenString8;
      return state.tokenize(stream, state);
    }
    if (ch === "{") {
      if (stream.eat("|")) {
        state.longString = true;
        state.tokenize = tokenLongString;
        return state.tokenize(stream, state);
      }
    }
    if (ch === "(") {
      if (stream.match(/^\*(?!\))/)) {
        state.commentLevel++;
        state.tokenize = tokenComment7;
        return state.tokenize(stream, state);
      }
    }
    if (ch === "~" || ch === "?") {
      stream.eatWhile(/\w/);
      return "variableName.special";
    }
    if (ch === "`") {
      stream.eatWhile(/\w/);
      return "quote";
    }
    if (ch === "/" && parserConfig.slashComments && stream.eat("/")) {
      stream.skipToEnd();
      return "comment";
    }
    if (/\d/.test(ch)) {
      if (ch === "0" && stream.eat(/[bB]/)) {
        stream.eatWhile(/[01]/);
      }
      if (ch === "0" && stream.eat(/[xX]/)) {
        stream.eatWhile(/[0-9a-fA-F]/);
      }
      if (ch === "0" && stream.eat(/[oO]/)) {
        stream.eatWhile(/[0-7]/);
      } else {
        stream.eatWhile(/[\d_]/);
        if (stream.eat(".")) {
          stream.eatWhile(/[\d]/);
        }
        if (stream.eat(/[eE]/)) {
          stream.eatWhile(/[\d\-+]/);
        }
      }
      return "number";
    }
    if (/[+\-*&%=<>!?|@\.~:]/.test(ch)) {
      return "operator";
    }
    if (/[\w\xa1-\uffff]/.test(ch)) {
      stream.eatWhile(/[\w\xa1-\uffff]/);
      var cur = stream.current();
      return words8.hasOwnProperty(cur) ? words8[cur] : "variable";
    }
    return null;
  }
  function tokenString8(stream, state) {
    var next, end = false, escaped = false;
    while ((next = stream.next()) != null) {
      if (next === '"' && !escaped) {
        end = true;
        break;
      }
      escaped = !escaped && next === "\\";
    }
    if (end && !escaped) {
      state.tokenize = tokenBase13;
    }
    return "string";
  }
  ;
  function tokenComment7(stream, state) {
    var prev, next;
    while (state.commentLevel > 0 && (next = stream.next()) != null) {
      if (prev === "(" && next === "*") state.commentLevel++;
      if (prev === "*" && next === ")") state.commentLevel--;
      prev = next;
    }
    if (state.commentLevel <= 0) {
      state.tokenize = tokenBase13;
    }
    return "comment";
  }
  function tokenLongString(stream, state) {
    var prev, next;
    while (state.longString && (next = stream.next()) != null) {
      if (prev === "|" && next === "}") state.longString = false;
      prev = next;
    }
    if (!state.longString) {
      state.tokenize = tokenBase13;
    }
    return "string";
  }
  return {
    startState: function() {
      return { tokenize: tokenBase13, commentLevel: 0, longString: false };
    },
    token: function(stream, state) {
      if (stream.eatSpace()) return null;
      return state.tokenize(stream, state);
    },
    languageData: {
      autocomplete: hintWords,
      commentTokens: {
        line: parserConfig.slashComments ? "//" : void 0,
        block: { open: "(*", close: "*)" }
      }
    }
  };
}
var oCaml = mlLike({
  name: "ocaml",
  extraWords: {
    "and": "keyword",
    "assert": "keyword",
    "begin": "keyword",
    "class": "keyword",
    "constraint": "keyword",
    "done": "keyword",
    "downto": "keyword",
    "external": "keyword",
    "function": "keyword",
    "initializer": "keyword",
    "lazy": "keyword",
    "match": "keyword",
    "method": "keyword",
    "module": "keyword",
    "mutable": "keyword",
    "new": "keyword",
    "nonrec": "keyword",
    "object": "keyword",
    "private": "keyword",
    "sig": "keyword",
    "to": "keyword",
    "try": "keyword",
    "value": "keyword",
    "virtual": "keyword",
    "when": "keyword",
    // builtins
    "raise": "builtin",
    "failwith": "builtin",
    "true": "builtin",
    "false": "builtin",
    // Pervasives builtins
    "asr": "builtin",
    "land": "builtin",
    "lor": "builtin",
    "lsl": "builtin",
    "lsr": "builtin",
    "lxor": "builtin",
    "mod": "builtin",
    "or": "builtin",
    // More Pervasives
    "raise_notrace": "builtin",
    "trace": "builtin",
    "exit": "builtin",
    "print_string": "builtin",
    "print_endline": "builtin",
    "int": "type",
    "float": "type",
    "bool": "type",
    "char": "type",
    "string": "type",
    "unit": "type",
    // Modules
    "List": "builtin"
  }
});
var fSharp = mlLike({
  name: "fsharp",
  extraWords: {
    "abstract": "keyword",
    "assert": "keyword",
    "base": "keyword",
    "begin": "keyword",
    "class": "keyword",
    "default": "keyword",
    "delegate": "keyword",
    "do!": "keyword",
    "done": "keyword",
    "downcast": "keyword",
    "downto": "keyword",
    "elif": "keyword",
    "extern": "keyword",
    "finally": "keyword",
    "for": "keyword",
    "function": "keyword",
    "global": "keyword",
    "inherit": "keyword",
    "inline": "keyword",
    "interface": "keyword",
    "internal": "keyword",
    "lazy": "keyword",
    "let!": "keyword",
    "match": "keyword",
    "member": "keyword",
    "module": "keyword",
    "mutable": "keyword",
    "namespace": "keyword",
    "new": "keyword",
    "null": "keyword",
    "override": "keyword",
    "private": "keyword",
    "public": "keyword",
    "return!": "keyword",
    "return": "keyword",
    "select": "keyword",
    "static": "keyword",
    "to": "keyword",
    "try": "keyword",
    "upcast": "keyword",
    "use!": "keyword",
    "use": "keyword",
    "void": "keyword",
    "when": "keyword",
    "yield!": "keyword",
    "yield": "keyword",
    // Reserved words
    "atomic": "keyword",
    "break": "keyword",
    "checked": "keyword",
    "component": "keyword",
    "const": "keyword",
    "constraint": "keyword",
    "constructor": "keyword",
    "continue": "keyword",
    "eager": "keyword",
    "event": "keyword",
    "external": "keyword",
    "fixed": "keyword",
    "method": "keyword",
    "mixin": "keyword",
    "object": "keyword",
    "parallel": "keyword",
    "process": "keyword",
    "protected": "keyword",
    "pure": "keyword",
    "sealed": "keyword",
    "tailcall": "keyword",
    "trait": "keyword",
    "virtual": "keyword",
    "volatile": "keyword",
    // builtins
    "List": "builtin",
    "Seq": "builtin",
    "Map": "builtin",
    "Set": "builtin",
    "Option": "builtin",
    "int": "builtin",
    "string": "builtin",
    "not": "builtin",
    "true": "builtin",
    "false": "builtin",
    "raise": "builtin",
    "failwith": "builtin"
  },
  slashComments: true
});
var sml = mlLike({
  name: "sml",
  extraWords: {
    "abstype": "keyword",
    "and": "keyword",
    "andalso": "keyword",
    "case": "keyword",
    "datatype": "keyword",
    "fn": "keyword",
    "handle": "keyword",
    "infix": "keyword",
    "infixr": "keyword",
    "local": "keyword",
    "nonfix": "keyword",
    "op": "keyword",
    "orelse": "keyword",
    "raise": "keyword",
    "withtype": "keyword",
    "eqtype": "keyword",
    "sharing": "keyword",
    "sig": "keyword",
    "signature": "keyword",
    "structure": "keyword",
    "where": "keyword",
    "true": "keyword",
    "false": "keyword",
    // types
    "int": "builtin",
    "real": "builtin",
    "string": "builtin",
    "char": "builtin",
    "bool": "builtin"
  },
  slashComments: true
});

// node_modules/@codemirror/legacy-modes/mode/nginx.js
function words3(str) {
  var obj = {}, words8 = str.split(" ");
  for (var i = 0; i < words8.length; ++i) obj[words8[i]] = true;
  return obj;
}
var keywords5 = words3(
  /* ngxDirectiveControl */
  "break return rewrite set accept_mutex accept_mutex_delay access_log add_after_body add_before_body add_header addition_types aio alias allow ancient_browser ancient_browser_value auth_basic auth_basic_user_file auth_http auth_http_header auth_http_timeout autoindex autoindex_exact_size autoindex_localtime charset charset_types client_body_buffer_size client_body_in_file_only client_body_in_single_buffer client_body_temp_path client_body_timeout client_header_buffer_size client_header_timeout client_max_body_size connection_pool_size create_full_put_path daemon dav_access dav_methods debug_connection debug_points default_type degradation degrade deny devpoll_changes devpoll_events directio directio_alignment empty_gif env epoll_events error_log eventport_events expires fastcgi_bind fastcgi_buffer_size fastcgi_buffers fastcgi_busy_buffers_size fastcgi_cache fastcgi_cache_key fastcgi_cache_methods fastcgi_cache_min_uses fastcgi_cache_path fastcgi_cache_use_stale fastcgi_cache_valid fastcgi_catch_stderr fastcgi_connect_timeout fastcgi_hide_header fastcgi_ignore_client_abort fastcgi_ignore_headers fastcgi_index fastcgi_intercept_errors fastcgi_max_temp_file_size fastcgi_next_upstream fastcgi_param fastcgi_pass_header fastcgi_pass_request_body fastcgi_pass_request_headers fastcgi_read_timeout fastcgi_send_lowat fastcgi_send_timeout fastcgi_split_path_info fastcgi_store fastcgi_store_access fastcgi_temp_file_write_size fastcgi_temp_path fastcgi_upstream_fail_timeout fastcgi_upstream_max_fails flv geoip_city geoip_country google_perftools_profiles gzip gzip_buffers gzip_comp_level gzip_disable gzip_hash gzip_http_version gzip_min_length gzip_no_buffer gzip_proxied gzip_static gzip_types gzip_vary gzip_window if_modified_since ignore_invalid_headers image_filter image_filter_buffer image_filter_jpeg_quality image_filter_transparency imap_auth imap_capabilities imap_client_buffer index ip_hash keepalive_requests keepalive_timeout kqueue_changes kqueue_events large_client_header_buffers limit_conn limit_conn_log_level limit_rate limit_rate_after limit_req limit_req_log_level limit_req_zone limit_zone lingering_time lingering_timeout lock_file log_format log_not_found log_subrequest map_hash_bucket_size map_hash_max_size master_process memcached_bind memcached_buffer_size memcached_connect_timeout memcached_next_upstream memcached_read_timeout memcached_send_timeout memcached_upstream_fail_timeout memcached_upstream_max_fails merge_slashes min_delete_depth modern_browser modern_browser_value msie_padding msie_refresh multi_accept open_file_cache open_file_cache_errors open_file_cache_events open_file_cache_min_uses open_file_cache_valid open_log_file_cache output_buffers override_charset perl perl_modules perl_require perl_set pid pop3_auth pop3_capabilities port_in_redirect postpone_gzipping postpone_output protocol proxy proxy_bind proxy_buffer proxy_buffer_size proxy_buffering proxy_buffers proxy_busy_buffers_size proxy_cache proxy_cache_key proxy_cache_methods proxy_cache_min_uses proxy_cache_path proxy_cache_use_stale proxy_cache_valid proxy_connect_timeout proxy_headers_hash_bucket_size proxy_headers_hash_max_size proxy_hide_header proxy_ignore_client_abort proxy_ignore_headers proxy_intercept_errors proxy_max_temp_file_size proxy_method proxy_next_upstream proxy_pass_error_message proxy_pass_header proxy_pass_request_body proxy_pass_request_headers proxy_read_timeout proxy_redirect proxy_send_lowat proxy_send_timeout proxy_set_body proxy_set_header proxy_ssl_session_reuse proxy_store proxy_store_access proxy_temp_file_write_size proxy_temp_path proxy_timeout proxy_upstream_fail_timeout proxy_upstream_max_fails random_index read_ahead real_ip_header recursive_error_pages request_pool_size reset_timedout_connection resolver resolver_timeout rewrite_log rtsig_overflow_events rtsig_overflow_test rtsig_overflow_threshold rtsig_signo satisfy secure_link_secret send_lowat send_timeout sendfile sendfile_max_chunk server_name_in_redirect server_names_hash_bucket_size server_names_hash_max_size server_tokens set_real_ip_from smtp_auth smtp_capabilities smtp_client_buffer smtp_greeting_delay so_keepalive source_charset ssi ssi_ignore_recycled_buffers ssi_min_file_chunk ssi_silent_errors ssi_types ssi_value_length ssl ssl_certificate ssl_certificate_key ssl_ciphers ssl_client_certificate ssl_crl ssl_dhparam ssl_engine ssl_prefer_server_ciphers ssl_protocols ssl_session_cache ssl_session_timeout ssl_verify_client ssl_verify_depth starttls stub_status sub_filter sub_filter_once sub_filter_types tcp_nodelay tcp_nopush thread_stack_size timeout timer_resolution types_hash_bucket_size types_hash_max_size underscores_in_headers uninitialized_variable_warn use user userid userid_domain userid_expires userid_mark userid_name userid_p3p userid_path userid_service valid_referers variables_hash_bucket_size variables_hash_max_size worker_connections worker_cpu_affinity worker_priority worker_processes worker_rlimit_core worker_rlimit_nofile worker_rlimit_sigpending worker_threads working_directory xclient xml_entities xslt_stylesheet xslt_typesdrew@li229-23"
);
var keywords_block = words3(
  /* ngxDirectiveBlock */
  "http mail events server types location upstream charset_map limit_except if geo map"
);
var keywords_important = words3(
  /* ngxDirectiveImportant */
  "include root server server_name listen internal proxy_pass memcached_pass fastcgi_pass try_files"
);
var type;
function ret(style, tp) {
  type = tp;
  return style;
}
function tokenBase4(stream, state) {
  stream.eatWhile(/[\w\$_]/);
  var cur = stream.current();
  if (keywords5.propertyIsEnumerable(cur)) {
    return "keyword";
  } else if (keywords_block.propertyIsEnumerable(cur)) {
    return "controlKeyword";
  } else if (keywords_important.propertyIsEnumerable(cur)) {
    return "controlKeyword";
  }
  var ch = stream.next();
  if (ch == "@") {
    stream.eatWhile(/[\w\\\-]/);
    return ret("meta", stream.current());
  } else if (ch == "/" && stream.eat("*")) {
    state.tokenize = tokenCComment;
    return tokenCComment(stream, state);
  } else if (ch == "<" && stream.eat("!")) {
    state.tokenize = tokenSGMLComment;
    return tokenSGMLComment(stream, state);
  } else if (ch == "=") ret(null, "compare");
  else if ((ch == "~" || ch == "|") && stream.eat("=")) return ret(null, "compare");
  else if (ch == '"' || ch == "'") {
    state.tokenize = tokenString3(ch);
    return state.tokenize(stream, state);
  } else if (ch == "#") {
    stream.skipToEnd();
    return ret("comment", "comment");
  } else if (ch == "!") {
    stream.match(/^\s*\w*/);
    return ret("keyword", "important");
  } else if (/\d/.test(ch)) {
    stream.eatWhile(/[\w.%]/);
    return ret("number", "unit");
  } else if (/[,.+>*\/]/.test(ch)) {
    return ret(null, "select-op");
  } else if (/[;{}:\[\]]/.test(ch)) {
    return ret(null, ch);
  } else {
    stream.eatWhile(/[\w\\\-]/);
    return ret("variable", "variable");
  }
}
function tokenCComment(stream, state) {
  var maybeEnd = false, ch;
  while ((ch = stream.next()) != null) {
    if (maybeEnd && ch == "/") {
      state.tokenize = tokenBase4;
      break;
    }
    maybeEnd = ch == "*";
  }
  return ret("comment", "comment");
}
function tokenSGMLComment(stream, state) {
  var dashes = 0, ch;
  while ((ch = stream.next()) != null) {
    if (dashes >= 2 && ch == ">") {
      state.tokenize = tokenBase4;
      break;
    }
    dashes = ch == "-" ? dashes + 1 : 0;
  }
  return ret("comment", "comment");
}
function tokenString3(quote) {
  return function(stream, state) {
    var escaped = false, ch;
    while ((ch = stream.next()) != null) {
      if (ch == quote && !escaped)
        break;
      escaped = !escaped && ch == "\\";
    }
    if (!escaped) state.tokenize = tokenBase4;
    return ret("string", "string");
  };
}
var nginx = {
  name: "nginx",
  startState: function() {
    return {
      tokenize: tokenBase4,
      baseIndent: 0,
      stack: []
    };
  },
  token: function(stream, state) {
    if (stream.eatSpace()) return null;
    type = null;
    var style = state.tokenize(stream, state);
    var context = state.stack[state.stack.length - 1];
    if (type == "hash" && context == "rule") style = "atom";
    else if (style == "variable") {
      if (context == "rule") style = "number";
      else if (!context || context == "@media{") style = "tag";
    }
    if (context == "rule" && /^[\{\};]$/.test(type))
      state.stack.pop();
    if (type == "{") {
      if (context == "@media") state.stack[state.stack.length - 1] = "@media{";
      else state.stack.push("{");
    } else if (type == "}") state.stack.pop();
    else if (type == "@media") state.stack.push("@media");
    else if (context == "{" && type != "comment") state.stack.push("rule");
    return style;
  },
  indent: function(state, textAfter, cx) {
    var n = state.stack.length;
    if (/^\}/.test(textAfter))
      n -= state.stack[state.stack.length - 1] == "rule" ? 2 : 1;
    return state.baseIndent + n * cx.unit;
  },
  languageData: {
    indentOnInput: /^\s*\}$/
  }
};

// node_modules/@codemirror/legacy-modes/mode/octave.js
function wordRegexp2(words8) {
  return new RegExp("^((" + words8.join(")|(") + "))\\b");
}
var singleOperators = new RegExp("^[\\+\\-\\*/&|\\^~<>!@'\\\\]");
var singleDelimiters = new RegExp("^[\\(\\[\\{\\},:=;\\.]");
var doubleOperators = new RegExp("^((==)|(~=)|(<=)|(>=)|(<<)|(>>)|(\\.[\\+\\-\\*/\\^\\\\]))");
var doubleDelimiters = new RegExp("^((!=)|(\\+=)|(\\-=)|(\\*=)|(/=)|(&=)|(\\|=)|(\\^=))");
var tripleDelimiters = new RegExp("^((>>=)|(<<=))");
var expressionEnd = new RegExp("^[\\]\\)]");
var identifiers2 = new RegExp("^[_A-Za-z\xA1-\uFFFF][_A-Za-z0-9\xA1-\uFFFF]*");
var builtins4 = wordRegexp2([
  "error",
  "eval",
  "function",
  "abs",
  "acos",
  "atan",
  "asin",
  "cos",
  "cosh",
  "exp",
  "log",
  "prod",
  "sum",
  "log10",
  "max",
  "min",
  "sign",
  "sin",
  "sinh",
  "sqrt",
  "tan",
  "reshape",
  "break",
  "zeros",
  "default",
  "margin",
  "round",
  "ones",
  "rand",
  "syn",
  "ceil",
  "floor",
  "size",
  "clear",
  "zeros",
  "eye",
  "mean",
  "std",
  "cov",
  "det",
  "eig",
  "inv",
  "norm",
  "rank",
  "trace",
  "expm",
  "logm",
  "sqrtm",
  "linspace",
  "plot",
  "title",
  "xlabel",
  "ylabel",
  "legend",
  "text",
  "grid",
  "meshgrid",
  "mesh",
  "num2str",
  "fft",
  "ifft",
  "arrayfun",
  "cellfun",
  "input",
  "fliplr",
  "flipud",
  "ismember"
]);
var keywords6 = wordRegexp2([
  "return",
  "case",
  "switch",
  "else",
  "elseif",
  "end",
  "endif",
  "endfunction",
  "if",
  "otherwise",
  "do",
  "for",
  "while",
  "try",
  "catch",
  "classdef",
  "properties",
  "events",
  "methods",
  "global",
  "persistent",
  "endfor",
  "endwhile",
  "printf",
  "sprintf",
  "disp",
  "until",
  "continue",
  "pkg"
]);
function tokenTranspose(stream, state) {
  if (!stream.sol() && stream.peek() === "'") {
    stream.next();
    state.tokenize = tokenBase5;
    return "operator";
  }
  state.tokenize = tokenBase5;
  return tokenBase5(stream, state);
}
function tokenComment3(stream, state) {
  if (stream.match(/^.*%}/)) {
    state.tokenize = tokenBase5;
    return "comment";
  }
  ;
  stream.skipToEnd();
  return "comment";
}
function tokenBase5(stream, state) {
  if (stream.eatSpace()) return null;
  if (stream.match("%{")) {
    state.tokenize = tokenComment3;
    stream.skipToEnd();
    return "comment";
  }
  if (stream.match(/^[%#]/)) {
    stream.skipToEnd();
    return "comment";
  }
  if (stream.match(/^[0-9\.+-]/, false)) {
    if (stream.match(/^[+-]?0x[0-9a-fA-F]+[ij]?/)) {
      stream.tokenize = tokenBase5;
      return "number";
    }
    ;
    if (stream.match(/^[+-]?\d*\.\d+([EeDd][+-]?\d+)?[ij]?/)) {
      return "number";
    }
    ;
    if (stream.match(/^[+-]?\d+([EeDd][+-]?\d+)?[ij]?/)) {
      return "number";
    }
    ;
  }
  if (stream.match(wordRegexp2(["nan", "NaN", "inf", "Inf"]))) {
    return "number";
  }
  ;
  var m = stream.match(/^"(?:[^"]|"")*("|$)/) || stream.match(/^'(?:[^']|'')*('|$)/);
  if (m) {
    return m[1] ? "string" : "error";
  }
  if (stream.match(keywords6)) {
    return "keyword";
  }
  ;
  if (stream.match(builtins4)) {
    return "builtin";
  }
  ;
  if (stream.match(identifiers2)) {
    return "variable";
  }
  ;
  if (stream.match(singleOperators) || stream.match(doubleOperators)) {
    return "operator";
  }
  ;
  if (stream.match(singleDelimiters) || stream.match(doubleDelimiters) || stream.match(tripleDelimiters)) {
    return null;
  }
  ;
  if (stream.match(expressionEnd)) {
    state.tokenize = tokenTranspose;
    return null;
  }
  ;
  stream.next();
  return "error";
}
var octave = {
  name: "octave",
  startState: function() {
    return {
      tokenize: tokenBase5
    };
  },
  token: function(stream, state) {
    var style = state.tokenize(stream, state);
    if (style === "number" || style === "variable") {
      state.tokenize = tokenTranspose;
    }
    return style;
  },
  languageData: {
    commentTokens: { line: "%" }
  }
};

// node_modules/@codemirror/legacy-modes/mode/perl.js
function look(stream, c2) {
  return stream.string.charAt(stream.pos + (c2 || 0));
}
function prefix(stream, c2) {
  if (c2) {
    var x = stream.pos - c2;
    return stream.string.substr(x >= 0 ? x : 0, c2);
  } else {
    return stream.string.substr(0, stream.pos - 1);
  }
}
function suffix(stream, c2) {
  var y = stream.string.length;
  var x = y - stream.pos + 1;
  return stream.string.substr(stream.pos, c2 && c2 < y ? c2 : x);
}
function eatSuffix(stream, c2) {
  var x = stream.pos + c2;
  var y;
  if (x <= 0)
    stream.pos = 0;
  else if (x >= (y = stream.string.length - 1))
    stream.pos = y;
  else
    stream.pos = x;
}
var PERL = {
  //   null - magic touch
  //   1 - keyword
  //   2 - def
  //   3 - atom
  //   4 - operator
  //   5 - builtin (predefined)
  //   [x,y] - x=1,2,3; y=must be defined if x{...}
  //      PERL operators
  "->": 4,
  "++": 4,
  "--": 4,
  "**": 4,
  //   ! ~ \ and unary + and -
  "=~": 4,
  "!~": 4,
  "*": 4,
  "/": 4,
  "%": 4,
  "x": 4,
  "+": 4,
  "-": 4,
  ".": 4,
  "<<": 4,
  ">>": 4,
  //   named unary operators
  "<": 4,
  ">": 4,
  "<=": 4,
  ">=": 4,
  "lt": 4,
  "gt": 4,
  "le": 4,
  "ge": 4,
  "==": 4,
  "!=": 4,
  "<=>": 4,
  "eq": 4,
  "ne": 4,
  "cmp": 4,
  "~~": 4,
  "&": 4,
  "|": 4,
  "^": 4,
  "&&": 4,
  "||": 4,
  "//": 4,
  "..": 4,
  "...": 4,
  "?": 4,
  ":": 4,
  "=": 4,
  "+=": 4,
  "-=": 4,
  "*=": 4,
  //   etc. ???
  ",": 4,
  "=>": 4,
  "::": 4,
  //   list operators (rightward)
  "not": 4,
  "and": 4,
  "or": 4,
  "xor": 4,
  //      PERL predefined variables (I know, what this is a paranoid idea, but may be needed for people, who learn PERL, and for me as well, ...and may be for you?;)
  "BEGIN": [5, 1],
  "END": [5, 1],
  "PRINT": [5, 1],
  "PRINTF": [5, 1],
  "GETC": [5, 1],
  "READ": [5, 1],
  "READLINE": [5, 1],
  "DESTROY": [5, 1],
  "TIE": [5, 1],
  "TIEHANDLE": [5, 1],
  "UNTIE": [5, 1],
  "STDIN": 5,
  "STDIN_TOP": 5,
  "STDOUT": 5,
  "STDOUT_TOP": 5,
  "STDERR": 5,
  "STDERR_TOP": 5,
  "$ARG": 5,
  "$_": 5,
  "@ARG": 5,
  "@_": 5,
  "$LIST_SEPARATOR": 5,
  '$"': 5,
  "$PROCESS_ID": 5,
  "$PID": 5,
  "$$": 5,
  "$REAL_GROUP_ID": 5,
  "$GID": 5,
  "$(": 5,
  "$EFFECTIVE_GROUP_ID": 5,
  "$EGID": 5,
  "$)": 5,
  "$PROGRAM_NAME": 5,
  "$0": 5,
  "$SUBSCRIPT_SEPARATOR": 5,
  "$SUBSEP": 5,
  "$;": 5,
  "$REAL_USER_ID": 5,
  "$UID": 5,
  "$<": 5,
  "$EFFECTIVE_USER_ID": 5,
  "$EUID": 5,
  "$>": 5,
  "$a": 5,
  "$b": 5,
  "$COMPILING": 5,
  "$^C": 5,
  "$DEBUGGING": 5,
  "$^D": 5,
  "${^ENCODING}": 5,
  "$ENV": 5,
  "%ENV": 5,
  "$SYSTEM_FD_MAX": 5,
  "$^F": 5,
  "@F": 5,
  "${^GLOBAL_PHASE}": 5,
  "$^H": 5,
  "%^H": 5,
  "@INC": 5,
  "%INC": 5,
  "$INPLACE_EDIT": 5,
  "$^I": 5,
  "$^M": 5,
  "$OSNAME": 5,
  "$^O": 5,
  "${^OPEN}": 5,
  "$PERLDB": 5,
  "$^P": 5,
  "$SIG": 5,
  "%SIG": 5,
  "$BASETIME": 5,
  "$^T": 5,
  "${^TAINT}": 5,
  "${^UNICODE}": 5,
  "${^UTF8CACHE}": 5,
  "${^UTF8LOCALE}": 5,
  "$PERL_VERSION": 5,
  "$^V": 5,
  "${^WIN32_SLOPPY_STAT}": 5,
  "$EXECUTABLE_NAME": 5,
  "$^X": 5,
  "$1": 5,
  // - regexp $1, $2...
  "$MATCH": 5,
  "$&": 5,
  "${^MATCH}": 5,
  "$PREMATCH": 5,
  "$`": 5,
  "${^PREMATCH}": 5,
  "$POSTMATCH": 5,
  "$'": 5,
  "${^POSTMATCH}": 5,
  "$LAST_PAREN_MATCH": 5,
  "$+": 5,
  "$LAST_SUBMATCH_RESULT": 5,
  "$^N": 5,
  "@LAST_MATCH_END": 5,
  "@+": 5,
  "%LAST_PAREN_MATCH": 5,
  "%+": 5,
  "@LAST_MATCH_START": 5,
  "@-": 5,
  "%LAST_MATCH_START": 5,
  "%-": 5,
  "$LAST_REGEXP_CODE_RESULT": 5,
  "$^R": 5,
  "${^RE_DEBUG_FLAGS}": 5,
  "${^RE_TRIE_MAXBUF}": 5,
  "$ARGV": 5,
  "@ARGV": 5,
  "ARGV": 5,
  "ARGVOUT": 5,
  "$OUTPUT_FIELD_SEPARATOR": 5,
  "$OFS": 5,
  "$,": 5,
  "$INPUT_LINE_NUMBER": 5,
  "$NR": 5,
  "$.": 5,
  "$INPUT_RECORD_SEPARATOR": 5,
  "$RS": 5,
  "$/": 5,
  "$OUTPUT_RECORD_SEPARATOR": 5,
  "$ORS": 5,
  "$\\": 5,
  "$OUTPUT_AUTOFLUSH": 5,
  "$|": 5,
  "$ACCUMULATOR": 5,
  "$^A": 5,
  "$FORMAT_FORMFEED": 5,
  "$^L": 5,
  "$FORMAT_PAGE_NUMBER": 5,
  "$%": 5,
  "$FORMAT_LINES_LEFT": 5,
  "$-": 5,
  "$FORMAT_LINE_BREAK_CHARACTERS": 5,
  "$:": 5,
  "$FORMAT_LINES_PER_PAGE": 5,
  "$=": 5,
  "$FORMAT_TOP_NAME": 5,
  "$^": 5,
  "$FORMAT_NAME": 5,
  "$~": 5,
  "${^CHILD_ERROR_NATIVE}": 5,
  "$EXTENDED_OS_ERROR": 5,
  "$^E": 5,
  "$EXCEPTIONS_BEING_CAUGHT": 5,
  "$^S": 5,
  "$WARNING": 5,
  "$^W": 5,
  "${^WARNING_BITS}": 5,
  "$OS_ERROR": 5,
  "$ERRNO": 5,
  "$!": 5,
  "%OS_ERROR": 5,
  "%ERRNO": 5,
  "%!": 5,
  "$CHILD_ERROR": 5,
  "$?": 5,
  "$EVAL_ERROR": 5,
  "$@": 5,
  "$OFMT": 5,
  "$#": 5,
  "$*": 5,
  "$ARRAY_BASE": 5,
  "$[": 5,
  "$OLD_PERL_VERSION": 5,
  "$]": 5,
  //      PERL blocks
  "if": [1, 1],
  elsif: [1, 1],
  "else": [1, 1],
  "while": [1, 1],
  unless: [1, 1],
  "for": [1, 1],
  foreach: [1, 1],
  //      PERL functions
  "abs": 1,
  // - absolute value function
  accept: 1,
  // - accept an incoming socket connect
  alarm: 1,
  // - schedule a SIGALRM
  "atan2": 1,
  // - arctangent of Y/X in the range -PI to PI
  bind: 1,
  // - binds an address to a socket
  binmode: 1,
  // - prepare binary files for I/O
  bless: 1,
  // - create an object
  bootstrap: 1,
  //
  "break": 1,
  // - break out of a "given" block
  caller: 1,
  // - get context of the current subroutine call
  chdir: 1,
  // - change your current working directory
  chmod: 1,
  // - changes the permissions on a list of files
  chomp: 1,
  // - remove a trailing record separator from a string
  chop: 1,
  // - remove the last character from a string
  chown: 1,
  // - change the ownership on a list of files
  chr: 1,
  // - get character this number represents
  chroot: 1,
  // - make directory new root for path lookups
  close: 1,
  // - close file (or pipe or socket) handle
  closedir: 1,
  // - close directory handle
  connect: 1,
  // - connect to a remote socket
  "continue": [1, 1],
  // - optional trailing block in a while or foreach
  "cos": 1,
  // - cosine function
  crypt: 1,
  // - one-way passwd-style encryption
  dbmclose: 1,
  // - breaks binding on a tied dbm file
  dbmopen: 1,
  // - create binding on a tied dbm file
  "default": 1,
  //
  defined: 1,
  // - test whether a value, variable, or function is defined
  "delete": 1,
  // - deletes a value from a hash
  die: 1,
  // - raise an exception or bail out
  "do": 1,
  // - turn a BLOCK into a TERM
  dump: 1,
  // - create an immediate core dump
  each: 1,
  // - retrieve the next key/value pair from a hash
  endgrent: 1,
  // - be done using group file
  endhostent: 1,
  // - be done using hosts file
  endnetent: 1,
  // - be done using networks file
  endprotoent: 1,
  // - be done using protocols file
  endpwent: 1,
  // - be done using passwd file
  endservent: 1,
  // - be done using services file
  eof: 1,
  // - test a filehandle for its end
  "eval": 1,
  // - catch exceptions or compile and run code
  "exec": 1,
  // - abandon this program to run another
  exists: 1,
  // - test whether a hash key is present
  exit: 1,
  // - terminate this program
  "exp": 1,
  // - raise I to a power
  fcntl: 1,
  // - file control system call
  fileno: 1,
  // - return file descriptor from filehandle
  flock: 1,
  // - lock an entire file with an advisory lock
  fork: 1,
  // - create a new process just like this one
  format: 1,
  // - declare a picture format with use by the write() function
  formline: 1,
  // - internal function used for formats
  getc: 1,
  // - get the next character from the filehandle
  getgrent: 1,
  // - get next group record
  getgrgid: 1,
  // - get group record given group user ID
  getgrnam: 1,
  // - get group record given group name
  gethostbyaddr: 1,
  // - get host record given its address
  gethostbyname: 1,
  // - get host record given name
  gethostent: 1,
  // - get next hosts record
  getlogin: 1,
  // - return who logged in at this tty
  getnetbyaddr: 1,
  // - get network record given its address
  getnetbyname: 1,
  // - get networks record given name
  getnetent: 1,
  // - get next networks record
  getpeername: 1,
  // - find the other end of a socket connection
  getpgrp: 1,
  // - get process group
  getppid: 1,
  // - get parent process ID
  getpriority: 1,
  // - get current nice value
  getprotobyname: 1,
  // - get protocol record given name
  getprotobynumber: 1,
  // - get protocol record numeric protocol
  getprotoent: 1,
  // - get next protocols record
  getpwent: 1,
  // - get next passwd record
  getpwnam: 1,
  // - get passwd record given user login name
  getpwuid: 1,
  // - get passwd record given user ID
  getservbyname: 1,
  // - get services record given its name
  getservbyport: 1,
  // - get services record given numeric port
  getservent: 1,
  // - get next services record
  getsockname: 1,
  // - retrieve the sockaddr for a given socket
  getsockopt: 1,
  // - get socket options on a given socket
  given: 1,
  //
  glob: 1,
  // - expand filenames using wildcards
  gmtime: 1,
  // - convert UNIX time into record or string using Greenwich time
  "goto": 1,
  // - create spaghetti code
  grep: 1,
  // - locate elements in a list test true against a given criterion
  hex: 1,
  // - convert a string to a hexadecimal number
  "import": 1,
  // - patch a module's namespace into your own
  index: 1,
  // - find a substring within a string
  "int": 1,
  // - get the integer portion of a number
  ioctl: 1,
  // - system-dependent device control system call
  "join": 1,
  // - join a list into a string using a separator
  keys: 1,
  // - retrieve list of indices from a hash
  kill: 1,
  // - send a signal to a process or process group
  last: 1,
  // - exit a block prematurely
  lc: 1,
  // - return lower-case version of a string
  lcfirst: 1,
  // - return a string with just the next letter in lower case
  length: 1,
  // - return the number of bytes in a string
  "link": 1,
  // - create a hard link in the filesystem
  listen: 1,
  // - register your socket as a server
  local: 2,
  // - create a temporary value for a global variable (dynamic scoping)
  localtime: 1,
  // - convert UNIX time into record or string using local time
  lock: 1,
  // - get a thread lock on a variable, subroutine, or method
  "log": 1,
  // - retrieve the natural logarithm for a number
  lstat: 1,
  // - stat a symbolic link
  m: null,
  // - match a string with a regular expression pattern
  map: 1,
  // - apply a change to a list to get back a new list with the changes
  mkdir: 1,
  // - create a directory
  msgctl: 1,
  // - SysV IPC message control operations
  msgget: 1,
  // - get SysV IPC message queue
  msgrcv: 1,
  // - receive a SysV IPC message from a message queue
  msgsnd: 1,
  // - send a SysV IPC message to a message queue
  my: 2,
  // - declare and assign a local variable (lexical scoping)
  "new": 1,
  //
  next: 1,
  // - iterate a block prematurely
  no: 1,
  // - unimport some module symbols or semantics at compile time
  oct: 1,
  // - convert a string to an octal number
  open: 1,
  // - open a file, pipe, or descriptor
  opendir: 1,
  // - open a directory
  ord: 1,
  // - find a character's numeric representation
  our: 2,
  // - declare and assign a package variable (lexical scoping)
  pack: 1,
  // - convert a list into a binary representation
  "package": 1,
  // - declare a separate global namespace
  pipe: 1,
  // - open a pair of connected filehandles
  pop: 1,
  // - remove the last element from an array and return it
  pos: 1,
  // - find or set the offset for the last/next m//g search
  print: 1,
  // - output a list to a filehandle
  printf: 1,
  // - output a formatted list to a filehandle
  prototype: 1,
  // - get the prototype (if any) of a subroutine
  push: 1,
  // - append one or more elements to an array
  q: null,
  // - singly quote a string
  qq: null,
  // - doubly quote a string
  qr: null,
  // - Compile pattern
  quotemeta: null,
  // - quote regular expression magic characters
  qw: null,
  // - quote a list of words
  qx: null,
  // - backquote quote a string
  rand: 1,
  // - retrieve the next pseudorandom number
  read: 1,
  // - fixed-length buffered input from a filehandle
  readdir: 1,
  // - get a directory from a directory handle
  readline: 1,
  // - fetch a record from a file
  readlink: 1,
  // - determine where a symbolic link is pointing
  readpipe: 1,
  // - execute a system command and collect standard output
  recv: 1,
  // - receive a message over a Socket
  redo: 1,
  // - start this loop iteration over again
  ref: 1,
  // - find out the type of thing being referenced
  rename: 1,
  // - change a filename
  require: 1,
  // - load in external functions from a library at runtime
  reset: 1,
  // - clear all variables of a given name
  "return": 1,
  // - get out of a function early
  reverse: 1,
  // - flip a string or a list
  rewinddir: 1,
  // - reset directory handle
  rindex: 1,
  // - right-to-left substring search
  rmdir: 1,
  // - remove a directory
  s: null,
  // - replace a pattern with a string
  say: 1,
  // - print with newline
  scalar: 1,
  // - force a scalar context
  seek: 1,
  // - reposition file pointer for random-access I/O
  seekdir: 1,
  // - reposition directory pointer
  select: 1,
  // - reset default output or do I/O multiplexing
  semctl: 1,
  // - SysV semaphore control operations
  semget: 1,
  // - get set of SysV semaphores
  semop: 1,
  // - SysV semaphore operations
  send: 1,
  // - send a message over a socket
  setgrent: 1,
  // - prepare group file for use
  sethostent: 1,
  // - prepare hosts file for use
  setnetent: 1,
  // - prepare networks file for use
  setpgrp: 1,
  // - set the process group of a process
  setpriority: 1,
  // - set a process's nice value
  setprotoent: 1,
  // - prepare protocols file for use
  setpwent: 1,
  // - prepare passwd file for use
  setservent: 1,
  // - prepare services file for use
  setsockopt: 1,
  // - set some socket options
  shift: 1,
  // - remove the first element of an array, and return it
  shmctl: 1,
  // - SysV shared memory operations
  shmget: 1,
  // - get SysV shared memory segment identifier
  shmread: 1,
  // - read SysV shared memory
  shmwrite: 1,
  // - write SysV shared memory
  shutdown: 1,
  // - close down just half of a socket connection
  "sin": 1,
  // - return the sine of a number
  sleep: 1,
  // - block for some number of seconds
  socket: 1,
  // - create a socket
  socketpair: 1,
  // - create a pair of sockets
  "sort": 1,
  // - sort a list of values
  splice: 1,
  // - add or remove elements anywhere in an array
  "split": 1,
  // - split up a string using a regexp delimiter
  sprintf: 1,
  // - formatted print into a string
  "sqrt": 1,
  // - square root function
  srand: 1,
  // - seed the random number generator
  stat: 1,
  // - get a file's status information
  state: 1,
  // - declare and assign a state variable (persistent lexical scoping)
  study: 1,
  // - optimize input data for repeated searches
  "sub": 1,
  // - declare a subroutine, possibly anonymously
  "substr": 1,
  // - get or alter a portion of a string
  symlink: 1,
  // - create a symbolic link to a file
  syscall: 1,
  // - execute an arbitrary system call
  sysopen: 1,
  // - open a file, pipe, or descriptor
  sysread: 1,
  // - fixed-length unbuffered input from a filehandle
  sysseek: 1,
  // - position I/O pointer on handle used with sysread and syswrite
  system: 1,
  // - run a separate program
  syswrite: 1,
  // - fixed-length unbuffered output to a filehandle
  tell: 1,
  // - get current seekpointer on a filehandle
  telldir: 1,
  // - get current seekpointer on a directory handle
  tie: 1,
  // - bind a variable to an object class
  tied: 1,
  // - get a reference to the object underlying a tied variable
  time: 1,
  // - return number of seconds since 1970
  times: 1,
  // - return elapsed time for self and child processes
  tr: null,
  // - transliterate a string
  truncate: 1,
  // - shorten a file
  uc: 1,
  // - return upper-case version of a string
  ucfirst: 1,
  // - return a string with just the next letter in upper case
  umask: 1,
  // - set file creation mode mask
  undef: 1,
  // - remove a variable or function definition
  unlink: 1,
  // - remove one link to a file
  unpack: 1,
  // - convert binary structure into normal perl variables
  unshift: 1,
  // - prepend more elements to the beginning of a list
  untie: 1,
  // - break a tie binding to a variable
  use: 1,
  // - load in a module at compile time
  utime: 1,
  // - set a file's last access and modify times
  values: 1,
  // - return a list of the values in a hash
  vec: 1,
  // - test or set particular bits in a string
  wait: 1,
  // - wait for any child process to die
  waitpid: 1,
  // - wait for a particular child process to die
  wantarray: 1,
  // - get void vs scalar vs list context of current subroutine call
  warn: 1,
  // - print debugging info
  when: 1,
  //
  write: 1,
  // - print a picture record
  y: null
};
var RXstyle = "string.special";
var RXmodifiers = /[goseximacplud]/;
function tokenChain(stream, state, chain3, style, tail) {
  state.chain = null;
  state.style = null;
  state.tail = null;
  state.tokenize = function(stream2, state2) {
    var e = false, c2, i = 0;
    while (c2 = stream2.next()) {
      if (c2 === chain3[i] && !e) {
        if (chain3[++i] !== void 0) {
          state2.chain = chain3[i];
          state2.style = style;
          state2.tail = tail;
        } else if (tail)
          stream2.eatWhile(tail);
        state2.tokenize = tokenPerl;
        return style;
      }
      e = !e && c2 == "\\";
    }
    return style;
  };
  return state.tokenize(stream, state);
}
function tokenSOMETHING(stream, state, string2) {
  state.tokenize = function(stream2, state2) {
    if (stream2.string == string2)
      state2.tokenize = tokenPerl;
    stream2.skipToEnd();
    return "string";
  };
  return state.tokenize(stream, state);
}
function tokenPerl(stream, state) {
  if (stream.eatSpace())
    return null;
  if (state.chain)
    return tokenChain(stream, state, state.chain, state.style, state.tail);
  if (stream.match(/^(\-?((\d[\d_]*)?\.\d+(e[+-]?\d+)?|\d+\.\d*)|0x[\da-fA-F_]+|0b[01_]+|\d[\d_]*(e[+-]?\d+)?)/))
    return "number";
  if (stream.match(/^<<(?=[_a-zA-Z])/)) {
    stream.eatWhile(/\w/);
    return tokenSOMETHING(stream, state, stream.current().substr(2));
  }
  if (stream.sol() && stream.match(/^\=item(?!\w)/)) {
    return tokenSOMETHING(stream, state, "=cut");
  }
  var ch = stream.next();
  if (ch == '"' || ch == "'") {
    if (prefix(stream, 3) == "<<" + ch) {
      var p = stream.pos;
      stream.eatWhile(/\w/);
      var n = stream.current().substr(1);
      if (n && stream.eat(ch))
        return tokenSOMETHING(stream, state, n);
      stream.pos = p;
    }
    return tokenChain(stream, state, [ch], "string");
  }
  if (ch == "q") {
    var c2 = look(stream, -2);
    if (!(c2 && /\w/.test(c2))) {
      c2 = look(stream, 0);
      if (c2 == "x") {
        c2 = look(stream, 1);
        if (c2 == "(") {
          eatSuffix(stream, 2);
          return tokenChain(stream, state, [")"], RXstyle, RXmodifiers);
        }
        if (c2 == "[") {
          eatSuffix(stream, 2);
          return tokenChain(stream, state, ["]"], RXstyle, RXmodifiers);
        }
        if (c2 == "{") {
          eatSuffix(stream, 2);
          return tokenChain(stream, state, ["}"], RXstyle, RXmodifiers);
        }
        if (c2 == "<") {
          eatSuffix(stream, 2);
          return tokenChain(stream, state, [">"], RXstyle, RXmodifiers);
        }
        if (/[\^'"!~\/]/.test(c2)) {
          eatSuffix(stream, 1);
          return tokenChain(stream, state, [stream.eat(c2)], RXstyle, RXmodifiers);
        }
      } else if (c2 == "q") {
        c2 = look(stream, 1);
        if (c2 == "(") {
          eatSuffix(stream, 2);
          return tokenChain(stream, state, [")"], "string");
        }
        if (c2 == "[") {
          eatSuffix(stream, 2);
          return tokenChain(stream, state, ["]"], "string");
        }
        if (c2 == "{") {
          eatSuffix(stream, 2);
          return tokenChain(stream, state, ["}"], "string");
        }
        if (c2 == "<") {
          eatSuffix(stream, 2);
          return tokenChain(stream, state, [">"], "string");
        }
        if (/[\^'"!~\/]/.test(c2)) {
          eatSuffix(stream, 1);
          return tokenChain(stream, state, [stream.eat(c2)], "string");
        }
      } else if (c2 == "w") {
        c2 = look(stream, 1);
        if (c2 == "(") {
          eatSuffix(stream, 2);
          return tokenChain(stream, state, [")"], "bracket");
        }
        if (c2 == "[") {
          eatSuffix(stream, 2);
          return tokenChain(stream, state, ["]"], "bracket");
        }
        if (c2 == "{") {
          eatSuffix(stream, 2);
          return tokenChain(stream, state, ["}"], "bracket");
        }
        if (c2 == "<") {
          eatSuffix(stream, 2);
          return tokenChain(stream, state, [">"], "bracket");
        }
        if (/[\^'"!~\/]/.test(c2)) {
          eatSuffix(stream, 1);
          return tokenChain(stream, state, [stream.eat(c2)], "bracket");
        }
      } else if (c2 == "r") {
        c2 = look(stream, 1);
        if (c2 == "(") {
          eatSuffix(stream, 2);
          return tokenChain(stream, state, [")"], RXstyle, RXmodifiers);
        }
        if (c2 == "[") {
          eatSuffix(stream, 2);
          return tokenChain(stream, state, ["]"], RXstyle, RXmodifiers);
        }
        if (c2 == "{") {
          eatSuffix(stream, 2);
          return tokenChain(stream, state, ["}"], RXstyle, RXmodifiers);
        }
        if (c2 == "<") {
          eatSuffix(stream, 2);
          return tokenChain(stream, state, [">"], RXstyle, RXmodifiers);
        }
        if (/[\^'"!~\/]/.test(c2)) {
          eatSuffix(stream, 1);
          return tokenChain(stream, state, [stream.eat(c2)], RXstyle, RXmodifiers);
        }
      } else if (/[\^'"!~\/(\[{<]/.test(c2)) {
        if (c2 == "(") {
          eatSuffix(stream, 1);
          return tokenChain(stream, state, [")"], "string");
        }
        if (c2 == "[") {
          eatSuffix(stream, 1);
          return tokenChain(stream, state, ["]"], "string");
        }
        if (c2 == "{") {
          eatSuffix(stream, 1);
          return tokenChain(stream, state, ["}"], "string");
        }
        if (c2 == "<") {
          eatSuffix(stream, 1);
          return tokenChain(stream, state, [">"], "string");
        }
        if (/[\^'"!~\/]/.test(c2)) {
          return tokenChain(stream, state, [stream.eat(c2)], "string");
        }
      }
    }
  }
  if (ch == "m") {
    var c2 = look(stream, -2);
    if (!(c2 && /\w/.test(c2))) {
      c2 = stream.eat(/[(\[{<\^'"!~\/]/);
      if (c2) {
        if (/[\^'"!~\/]/.test(c2)) {
          return tokenChain(stream, state, [c2], RXstyle, RXmodifiers);
        }
        if (c2 == "(") {
          return tokenChain(stream, state, [")"], RXstyle, RXmodifiers);
        }
        if (c2 == "[") {
          return tokenChain(stream, state, ["]"], RXstyle, RXmodifiers);
        }
        if (c2 == "{") {
          return tokenChain(stream, state, ["}"], RXstyle, RXmodifiers);
        }
        if (c2 == "<") {
          return tokenChain(stream, state, [">"], RXstyle, RXmodifiers);
        }
      }
    }
  }
  if (ch == "s") {
    var c2 = /[\/>\]})\w]/.test(look(stream, -2));
    if (!c2) {
      c2 = stream.eat(/[(\[{<\^'"!~\/]/);
      if (c2) {
        if (c2 == "[")
          return tokenChain(stream, state, ["]", "]"], RXstyle, RXmodifiers);
        if (c2 == "{")
          return tokenChain(stream, state, ["}", "}"], RXstyle, RXmodifiers);
        if (c2 == "<")
          return tokenChain(stream, state, [">", ">"], RXstyle, RXmodifiers);
        if (c2 == "(")
          return tokenChain(stream, state, [")", ")"], RXstyle, RXmodifiers);
        return tokenChain(stream, state, [c2, c2], RXstyle, RXmodifiers);
      }
    }
  }
  if (ch == "y") {
    var c2 = /[\/>\]})\w]/.test(look(stream, -2));
    if (!c2) {
      c2 = stream.eat(/[(\[{<\^'"!~\/]/);
      if (c2) {
        if (c2 == "[")
          return tokenChain(stream, state, ["]", "]"], RXstyle, RXmodifiers);
        if (c2 == "{")
          return tokenChain(stream, state, ["}", "}"], RXstyle, RXmodifiers);
        if (c2 == "<")
          return tokenChain(stream, state, [">", ">"], RXstyle, RXmodifiers);
        if (c2 == "(")
          return tokenChain(stream, state, [")", ")"], RXstyle, RXmodifiers);
        return tokenChain(stream, state, [c2, c2], RXstyle, RXmodifiers);
      }
    }
  }
  if (ch == "t") {
    var c2 = /[\/>\]})\w]/.test(look(stream, -2));
    if (!c2) {
      c2 = stream.eat("r");
      if (c2) {
        c2 = stream.eat(/[(\[{<\^'"!~\/]/);
        if (c2) {
          if (c2 == "[")
            return tokenChain(stream, state, ["]", "]"], RXstyle, RXmodifiers);
          if (c2 == "{")
            return tokenChain(stream, state, ["}", "}"], RXstyle, RXmodifiers);
          if (c2 == "<")
            return tokenChain(stream, state, [">", ">"], RXstyle, RXmodifiers);
          if (c2 == "(")
            return tokenChain(stream, state, [")", ")"], RXstyle, RXmodifiers);
          return tokenChain(stream, state, [c2, c2], RXstyle, RXmodifiers);
        }
      }
    }
  }
  if (ch == "`") {
    return tokenChain(stream, state, [ch], "builtin");
  }
  if (ch == "/") {
    if (!/~\s*$/.test(prefix(stream)))
      return "operator";
    else
      return tokenChain(stream, state, [ch], RXstyle, RXmodifiers);
  }
  if (ch == "$") {
    var p = stream.pos;
    if (stream.eatWhile(/\d/) || stream.eat("{") && stream.eatWhile(/\d/) && stream.eat("}"))
      return "builtin";
    else
      stream.pos = p;
  }
  if (/[$@%]/.test(ch)) {
    var p = stream.pos;
    if (stream.eat("^") && stream.eat(/[A-Z]/) || !/[@$%&]/.test(look(stream, -2)) && stream.eat(/[=|\\\-#?@;:&`~\^!\[\]*'"$+.,\/<>()]/)) {
      var c2 = stream.current();
      if (PERL[c2])
        return "builtin";
    }
    stream.pos = p;
  }
  if (/[$@%&]/.test(ch)) {
    if (stream.eatWhile(/[\w$]/) || stream.eat("{") && stream.eatWhile(/[\w$]/) && stream.eat("}")) {
      var c2 = stream.current();
      if (PERL[c2])
        return "builtin";
      else
        return "variable";
    }
  }
  if (ch == "#") {
    if (look(stream, -2) != "$") {
      stream.skipToEnd();
      return "comment";
    }
  }
  if (/[:+\-\^*$&%@=<>!?|\/~\.]/.test(ch)) {
    var p = stream.pos;
    stream.eatWhile(/[:+\-\^*$&%@=<>!?|\/~\.]/);
    if (PERL[stream.current()])
      return "operator";
    else
      stream.pos = p;
  }
  if (ch == "_") {
    if (stream.pos == 1) {
      if (suffix(stream, 6) == "_END__") {
        return tokenChain(stream, state, ["\0"], "comment");
      } else if (suffix(stream, 7) == "_DATA__") {
        return tokenChain(stream, state, ["\0"], "builtin");
      } else if (suffix(stream, 7) == "_C__") {
        return tokenChain(stream, state, ["\0"], "string");
      }
    }
  }
  if (/\w/.test(ch)) {
    var p = stream.pos;
    if (look(stream, -2) == "{" && (look(stream, 0) == "}" || stream.eatWhile(/\w/) && look(stream, 0) == "}"))
      return "string";
    else
      stream.pos = p;
  }
  if (/[A-Z]/.test(ch)) {
    var l = look(stream, -2);
    var p = stream.pos;
    stream.eatWhile(/[A-Z_]/);
    if (/[\da-z]/.test(look(stream, 0))) {
      stream.pos = p;
    } else {
      var c2 = PERL[stream.current()];
      if (!c2)
        return "meta";
      if (c2[1])
        c2 = c2[0];
      if (l != ":") {
        if (c2 == 1)
          return "keyword";
        else if (c2 == 2)
          return "def";
        else if (c2 == 3)
          return "atom";
        else if (c2 == 4)
          return "operator";
        else if (c2 == 5)
          return "builtin";
        else
          return "meta";
      } else
        return "meta";
    }
  }
  if (/[a-zA-Z_]/.test(ch)) {
    var l = look(stream, -2);
    stream.eatWhile(/\w/);
    var c2 = PERL[stream.current()];
    if (!c2)
      return "meta";
    if (c2[1])
      c2 = c2[0];
    if (l != ":") {
      if (c2 == 1)
        return "keyword";
      else if (c2 == 2)
        return "def";
      else if (c2 == 3)
        return "atom";
      else if (c2 == 4)
        return "operator";
      else if (c2 == 5)
        return "builtin";
      else
        return "meta";
    } else
      return "meta";
  }
  return null;
}
var perl = {
  name: "perl",
  startState: function() {
    return {
      tokenize: tokenPerl,
      chain: null,
      style: null,
      tail: null
    };
  },
  token: function(stream, state) {
    return (state.tokenize || tokenPerl)(stream, state);
  },
  languageData: {
    commentTokens: { line: "#" },
    wordChars: "$"
  }
};

// node_modules/@codemirror/legacy-modes/mode/pascal.js
function words4(str) {
  var obj = {}, words8 = str.split(" ");
  for (var i = 0; i < words8.length; ++i) obj[words8[i]] = true;
  return obj;
}
var keywords7 = words4(
  "absolute and array asm begin case const constructor destructor div do downto else end file for function goto if implementation in inherited inline interface label mod nil not object of operator or packed procedure program record reintroduce repeat self set shl shr string then to type unit until uses var while with xor as class dispinterface except exports finalization finally initialization inline is library on out packed property raise resourcestring threadvar try absolute abstract alias assembler bitpacked break cdecl continue cppdecl cvar default deprecated dynamic enumerator experimental export external far far16 forward generic helper implements index interrupt iocheck local message name near nodefault noreturn nostackframe oldfpccall otherwise overload override pascal platform private protected public published read register reintroduce result safecall saveregisters softfloat specialize static stdcall stored strict unaligned unimplemented varargs virtual write"
);
var atoms3 = { "null": true };
var isOperatorChar3 = /[+\-*&%=<>!?|\/]/;
function tokenBase6(stream, state) {
  var ch = stream.next();
  if (ch == "#" && state.startOfLine) {
    stream.skipToEnd();
    return "meta";
  }
  if (ch == '"' || ch == "'") {
    state.tokenize = tokenString4(ch);
    return state.tokenize(stream, state);
  }
  if (ch == "(" && stream.eat("*")) {
    state.tokenize = tokenComment4;
    return tokenComment4(stream, state);
  }
  if (ch == "{") {
    state.tokenize = tokenCommentBraces;
    return tokenCommentBraces(stream, state);
  }
  if (/[\[\]\(\),;\:\.]/.test(ch)) {
    return null;
  }
  if (/\d/.test(ch)) {
    stream.eatWhile(/[\w\.]/);
    return "number";
  }
  if (ch == "/") {
    if (stream.eat("/")) {
      stream.skipToEnd();
      return "comment";
    }
  }
  if (isOperatorChar3.test(ch)) {
    stream.eatWhile(isOperatorChar3);
    return "operator";
  }
  stream.eatWhile(/[\w\$_]/);
  var cur = stream.current().toLowerCase();
  if (keywords7.propertyIsEnumerable(cur)) return "keyword";
  if (atoms3.propertyIsEnumerable(cur)) return "atom";
  return "variable";
}
function tokenString4(quote) {
  return function(stream, state) {
    var escaped = false, next, end = false;
    while ((next = stream.next()) != null) {
      if (next == quote && !escaped) {
        end = true;
        break;
      }
      escaped = !escaped && next == "\\";
    }
    if (end || !escaped) state.tokenize = null;
    return "string";
  };
}
function tokenComment4(stream, state) {
  var maybeEnd = false, ch;
  while (ch = stream.next()) {
    if (ch == ")" && maybeEnd) {
      state.tokenize = null;
      break;
    }
    maybeEnd = ch == "*";
  }
  return "comment";
}
function tokenCommentBraces(stream, state) {
  var ch;
  while (ch = stream.next()) {
    if (ch == "}") {
      state.tokenize = null;
      break;
    }
  }
  return "comment";
}
var pascal = {
  name: "pascal",
  startState: function() {
    return { tokenize: null };
  },
  token: function(stream, state) {
    if (stream.eatSpace()) return null;
    var style = (state.tokenize || tokenBase6)(stream, state);
    if (style == "comment" || style == "meta") return style;
    return style;
  },
  languageData: {
    indentOnInput: /^\s*[{}]$/,
    commentTokens: { block: { open: "(*", close: "*)" } }
  }
};

// node_modules/@codemirror/legacy-modes/mode/powershell.js
function buildRegexp(patterns, options) {
  options = options || {};
  var prefix2 = options.prefix !== void 0 ? options.prefix : "^";
  var suffix2 = options.suffix !== void 0 ? options.suffix : "\\b";
  for (var i = 0; i < patterns.length; i++) {
    if (patterns[i] instanceof RegExp) {
      patterns[i] = patterns[i].source;
    } else {
      patterns[i] = patterns[i].replace(/[-\/\\^$*+?.()|[\]{}]/g, "\\$&");
    }
  }
  return new RegExp(prefix2 + "(" + patterns.join("|") + ")" + suffix2, "i");
}
var notCharacterOrDash = "(?=[^A-Za-z\\d\\-_]|$)";
var varNames = /[\w\-:]/;
var keywords8 = buildRegexp([
  /begin|break|catch|continue|data|default|do|dynamicparam/,
  /else|elseif|end|exit|filter|finally|for|foreach|from|function|if|in/,
  /param|process|return|switch|throw|trap|try|until|where|while/
], { suffix: notCharacterOrDash });
var punctuation = /[\[\]{},;`\\\.]|@[({]/;
var wordOperators = buildRegexp([
  "f",
  /b?not/,
  /[ic]?split/,
  "join",
  /is(not)?/,
  "as",
  /[ic]?(eq|ne|[gl][te])/,
  /[ic]?(not)?(like|match|contains)/,
  /[ic]?replace/,
  /b?(and|or|xor)/
], { prefix: "-" });
var symbolOperators2 = /[+\-*\/%]=|\+\+|--|\.\.|[+\-*&^%:=!|\/]|<(?!#)|(?!#)>/;
var operators2 = buildRegexp([wordOperators, symbolOperators2], { suffix: "" });
var numbers = /^((0x[\da-f]+)|((\d+\.\d+|\d\.|\.\d+|\d+)(e[\+\-]?\d+)?))[ld]?([kmgtp]b)?/i;
var identifiers3 = /^[A-Za-z\_][A-Za-z\-\_\d]*\b/;
var symbolBuiltins = /[A-Z]:|%|\?/i;
var namedBuiltins = buildRegexp([
  /Add-(Computer|Content|History|Member|PSSnapin|Type)/,
  /Checkpoint-Computer/,
  /Clear-(Content|EventLog|History|Host|Item(Property)?|Variable)/,
  /Compare-Object/,
  /Complete-Transaction/,
  /Connect-PSSession/,
  /ConvertFrom-(Csv|Json|SecureString|StringData)/,
  /Convert-Path/,
  /ConvertTo-(Csv|Html|Json|SecureString|Xml)/,
  /Copy-Item(Property)?/,
  /Debug-Process/,
  /Disable-(ComputerRestore|PSBreakpoint|PSRemoting|PSSessionConfiguration)/,
  /Disconnect-PSSession/,
  /Enable-(ComputerRestore|PSBreakpoint|PSRemoting|PSSessionConfiguration)/,
  /(Enter|Exit)-PSSession/,
  /Export-(Alias|Clixml|Console|Counter|Csv|FormatData|ModuleMember|PSSession)/,
  /ForEach-Object/,
  /Format-(Custom|List|Table|Wide)/,
  new RegExp("Get-(Acl|Alias|AuthenticodeSignature|ChildItem|Command|ComputerRestorePoint|Content|ControlPanelItem|Counter|Credential|Culture|Date|Event|EventLog|EventSubscriber|ExecutionPolicy|FormatData|Help|History|Host|HotFix|Item|ItemProperty|Job|Location|Member|Module|PfxCertificate|Process|PSBreakpoint|PSCallStack|PSDrive|PSProvider|PSSession|PSSessionConfiguration|PSSnapin|Random|Service|TraceSource|Transaction|TypeData|UICulture|Unique|Variable|Verb|WinEvent|WmiObject)"),
  /Group-Object/,
  /Import-(Alias|Clixml|Counter|Csv|LocalizedData|Module|PSSession)/,
  /ImportSystemModules/,
  /Invoke-(Command|Expression|History|Item|RestMethod|WebRequest|WmiMethod)/,
  /Join-Path/,
  /Limit-EventLog/,
  /Measure-(Command|Object)/,
  /Move-Item(Property)?/,
  new RegExp("New-(Alias|Event|EventLog|Item(Property)?|Module|ModuleManifest|Object|PSDrive|PSSession|PSSessionConfigurationFile|PSSessionOption|PSTransportOption|Service|TimeSpan|Variable|WebServiceProxy|WinEvent)"),
  /Out-(Default|File|GridView|Host|Null|Printer|String)/,
  /Pause/,
  /(Pop|Push)-Location/,
  /Read-Host/,
  /Receive-(Job|PSSession)/,
  /Register-(EngineEvent|ObjectEvent|PSSessionConfiguration|WmiEvent)/,
  /Remove-(Computer|Event|EventLog|Item(Property)?|Job|Module|PSBreakpoint|PSDrive|PSSession|PSSnapin|TypeData|Variable|WmiObject)/,
  /Rename-(Computer|Item(Property)?)/,
  /Reset-ComputerMachinePassword/,
  /Resolve-Path/,
  /Restart-(Computer|Service)/,
  /Restore-Computer/,
  /Resume-(Job|Service)/,
  /Save-Help/,
  /Select-(Object|String|Xml)/,
  /Send-MailMessage/,
  new RegExp("Set-(Acl|Alias|AuthenticodeSignature|Content|Date|ExecutionPolicy|Item(Property)?|Location|PSBreakpoint|PSDebug|PSSessionConfiguration|Service|StrictMode|TraceSource|Variable|WmiInstance)"),
  /Show-(Command|ControlPanelItem|EventLog)/,
  /Sort-Object/,
  /Split-Path/,
  /Start-(Job|Process|Service|Sleep|Transaction|Transcript)/,
  /Stop-(Computer|Job|Process|Service|Transcript)/,
  /Suspend-(Job|Service)/,
  /TabExpansion2/,
  /Tee-Object/,
  /Test-(ComputerSecureChannel|Connection|ModuleManifest|Path|PSSessionConfigurationFile)/,
  /Trace-Command/,
  /Unblock-File/,
  /Undo-Transaction/,
  /Unregister-(Event|PSSessionConfiguration)/,
  /Update-(FormatData|Help|List|TypeData)/,
  /Use-Transaction/,
  /Wait-(Event|Job|Process)/,
  /Where-Object/,
  /Write-(Debug|Error|EventLog|Host|Output|Progress|Verbose|Warning)/,
  /cd|help|mkdir|more|oss|prompt/,
  /ac|asnp|cat|cd|chdir|clc|clear|clhy|cli|clp|cls|clv|cnsn|compare|copy|cp|cpi|cpp|cvpa|dbp|del|diff|dir|dnsn|ebp/,
  /echo|epal|epcsv|epsn|erase|etsn|exsn|fc|fl|foreach|ft|fw|gal|gbp|gc|gci|gcm|gcs|gdr|ghy|gi|gjb|gl|gm|gmo|gp|gps/,
  /group|gsn|gsnp|gsv|gu|gv|gwmi|h|history|icm|iex|ihy|ii|ipal|ipcsv|ipmo|ipsn|irm|ise|iwmi|iwr|kill|lp|ls|man|md/,
  /measure|mi|mount|move|mp|mv|nal|ndr|ni|nmo|npssc|nsn|nv|ogv|oh|popd|ps|pushd|pwd|r|rbp|rcjb|rcsn|rd|rdr|ren|ri/,
  /rjb|rm|rmdir|rmo|rni|rnp|rp|rsn|rsnp|rujb|rv|rvpa|rwmi|sajb|sal|saps|sasv|sbp|sc|select|set|shcm|si|sl|sleep|sls/,
  /sort|sp|spjb|spps|spsv|start|sujb|sv|swmi|tee|trcm|type|where|wjb|write/
], { prefix: "", suffix: "" });
var variableBuiltins = buildRegexp([
  /[$?^_]|Args|ConfirmPreference|ConsoleFileName|DebugPreference|Error|ErrorActionPreference|ErrorView|ExecutionContext/,
  /FormatEnumerationLimit|Home|Host|Input|MaximumAliasCount|MaximumDriveCount|MaximumErrorCount|MaximumFunctionCount/,
  /MaximumHistoryCount|MaximumVariableCount|MyInvocation|NestedPromptLevel|OutputEncoding|Pid|Profile|ProgressPreference/,
  /PSBoundParameters|PSCommandPath|PSCulture|PSDefaultParameterValues|PSEmailServer|PSHome|PSScriptRoot|PSSessionApplicationName/,
  /PSSessionConfigurationName|PSSessionOption|PSUICulture|PSVersionTable|Pwd|ShellId|StackTrace|VerbosePreference/,
  /WarningPreference|WhatIfPreference/,
  /Event|EventArgs|EventSubscriber|Sender/,
  /Matches|Ofs|ForEach|LastExitCode|PSCmdlet|PSItem|PSSenderInfo|This/,
  /true|false|null/
], { prefix: "\\$", suffix: "" });
var builtins5 = buildRegexp([symbolBuiltins, namedBuiltins, variableBuiltins], { suffix: notCharacterOrDash });
var grammar = {
  keyword: keywords8,
  number: numbers,
  operator: operators2,
  builtin: builtins5,
  punctuation,
  variable: identifiers3
};
function tokenBase7(stream, state) {
  var parent = state.returnStack[state.returnStack.length - 1];
  if (parent && parent.shouldReturnFrom(state)) {
    state.tokenize = parent.tokenize;
    state.returnStack.pop();
    return state.tokenize(stream, state);
  }
  if (stream.eatSpace()) {
    return null;
  }
  if (stream.eat("(")) {
    state.bracketNesting += 1;
    return "punctuation";
  }
  if (stream.eat(")")) {
    state.bracketNesting -= 1;
    return "punctuation";
  }
  for (var key in grammar) {
    if (stream.match(grammar[key])) {
      return key;
    }
  }
  var ch = stream.next();
  if (ch === "'") {
    return tokenSingleQuoteString(stream, state);
  }
  if (ch === "$") {
    return tokenVariable(stream, state);
  }
  if (ch === '"') {
    return tokenDoubleQuoteString(stream, state);
  }
  if (ch === "<" && stream.eat("#")) {
    state.tokenize = tokenComment5;
    return tokenComment5(stream, state);
  }
  if (ch === "#") {
    stream.skipToEnd();
    return "comment";
  }
  if (ch === "@") {
    var quoteMatch = stream.eat(/["']/);
    if (quoteMatch && stream.eol()) {
      state.tokenize = tokenMultiString;
      state.startQuote = quoteMatch[0];
      return tokenMultiString(stream, state);
    } else if (stream.eol()) {
      return "error";
    } else if (stream.peek().match(/[({]/)) {
      return "punctuation";
    } else if (stream.peek().match(varNames)) {
      return tokenVariable(stream, state);
    }
  }
  return "error";
}
function tokenSingleQuoteString(stream, state) {
  var ch;
  while ((ch = stream.peek()) != null) {
    stream.next();
    if (ch === "'" && !stream.eat("'")) {
      state.tokenize = tokenBase7;
      return "string";
    }
  }
  return "error";
}
function tokenDoubleQuoteString(stream, state) {
  var ch;
  while ((ch = stream.peek()) != null) {
    if (ch === "$") {
      state.tokenize = tokenStringInterpolation;
      return "string";
    }
    stream.next();
    if (ch === "`") {
      stream.next();
      continue;
    }
    if (ch === '"' && !stream.eat('"')) {
      state.tokenize = tokenBase7;
      return "string";
    }
  }
  return "error";
}
function tokenStringInterpolation(stream, state) {
  return tokenInterpolation2(stream, state, tokenDoubleQuoteString);
}
function tokenMultiStringReturn(stream, state) {
  state.tokenize = tokenMultiString;
  state.startQuote = '"';
  return tokenMultiString(stream, state);
}
function tokenHereStringInterpolation(stream, state) {
  return tokenInterpolation2(stream, state, tokenMultiStringReturn);
}
function tokenInterpolation2(stream, state, parentTokenize) {
  if (stream.match("$(")) {
    var savedBracketNesting = state.bracketNesting;
    state.returnStack.push({
      /*jshint loopfunc:true */
      shouldReturnFrom: function(state2) {
        return state2.bracketNesting === savedBracketNesting;
      },
      tokenize: parentTokenize
    });
    state.tokenize = tokenBase7;
    state.bracketNesting += 1;
    return "punctuation";
  } else {
    stream.next();
    state.returnStack.push({
      shouldReturnFrom: function() {
        return true;
      },
      tokenize: parentTokenize
    });
    state.tokenize = tokenVariable;
    return state.tokenize(stream, state);
  }
}
function tokenComment5(stream, state) {
  var maybeEnd = false, ch;
  while ((ch = stream.next()) != null) {
    if (maybeEnd && ch == ">") {
      state.tokenize = tokenBase7;
      break;
    }
    maybeEnd = ch === "#";
  }
  return "comment";
}
function tokenVariable(stream, state) {
  var ch = stream.peek();
  if (stream.eat("{")) {
    state.tokenize = tokenVariableWithBraces;
    return tokenVariableWithBraces(stream, state);
  } else if (ch != void 0 && ch.match(varNames)) {
    stream.eatWhile(varNames);
    state.tokenize = tokenBase7;
    return "variable";
  } else {
    state.tokenize = tokenBase7;
    return "error";
  }
}
function tokenVariableWithBraces(stream, state) {
  var ch;
  while ((ch = stream.next()) != null) {
    if (ch === "}") {
      state.tokenize = tokenBase7;
      break;
    }
  }
  return "variable";
}
function tokenMultiString(stream, state) {
  var quote = state.startQuote;
  if (stream.sol() && stream.match(new RegExp(quote + "@"))) {
    state.tokenize = tokenBase7;
  } else if (quote === '"') {
    while (!stream.eol()) {
      var ch = stream.peek();
      if (ch === "$") {
        state.tokenize = tokenHereStringInterpolation;
        return "string";
      }
      stream.next();
      if (ch === "`") {
        stream.next();
      }
    }
  } else {
    stream.skipToEnd();
  }
  return "string";
}
var powerShell = {
  name: "powershell",
  startState: function() {
    return {
      returnStack: [],
      bracketNesting: 0,
      tokenize: tokenBase7
    };
  },
  token: function(stream, state) {
    return state.tokenize(stream, state);
  },
  languageData: {
    commentTokens: { line: "#", block: { open: "<#", close: "#>" } }
  }
};

// node_modules/@codemirror/legacy-modes/mode/properties.js
var properties = {
  name: "properties",
  token: function(stream, state) {
    var sol = stream.sol() || state.afterSection;
    var eol = stream.eol();
    state.afterSection = false;
    if (sol) {
      if (state.nextMultiline) {
        state.inMultiline = true;
        state.nextMultiline = false;
      } else {
        state.position = "def";
      }
    }
    if (eol && !state.nextMultiline) {
      state.inMultiline = false;
      state.position = "def";
    }
    if (sol) {
      while (stream.eatSpace()) {
      }
    }
    var ch = stream.next();
    if (sol && (ch === "#" || ch === "!" || ch === ";")) {
      state.position = "comment";
      stream.skipToEnd();
      return "comment";
    } else if (sol && ch === "[") {
      state.afterSection = true;
      stream.skipTo("]");
      stream.eat("]");
      return "header";
    } else if (ch === "=" || ch === ":") {
      state.position = "quote";
      return null;
    } else if (ch === "\\" && state.position === "quote") {
      if (stream.eol()) {
        state.nextMultiline = true;
      }
    }
    return state.position;
  },
  startState: function() {
    return {
      position: "def",
      // Current position, "def", "quote" or "comment"
      nextMultiline: false,
      // Is the next line multiline value
      inMultiline: false,
      // Is the current line a multiline value
      afterSection: false
      // Did we just open a section
    };
  }
};

// node_modules/@codemirror/legacy-modes/mode/python.js
function wordRegexp3(words8) {
  return new RegExp("^((" + words8.join(")|(") + "))\\b");
}
var wordOperators2 = wordRegexp3(["and", "or", "not", "is"]);
var commonKeywords = [
  "as",
  "assert",
  "break",
  "class",
  "continue",
  "def",
  "del",
  "elif",
  "else",
  "except",
  "finally",
  "for",
  "from",
  "global",
  "if",
  "import",
  "lambda",
  "pass",
  "raise",
  "return",
  "try",
  "while",
  "with",
  "yield",
  "in",
  "False",
  "True"
];
var commonBuiltins = [
  "abs",
  "all",
  "any",
  "bin",
  "bool",
  "bytearray",
  "callable",
  "chr",
  "classmethod",
  "compile",
  "complex",
  "delattr",
  "dict",
  "dir",
  "divmod",
  "enumerate",
  "eval",
  "filter",
  "float",
  "format",
  "frozenset",
  "getattr",
  "globals",
  "hasattr",
  "hash",
  "help",
  "hex",
  "id",
  "input",
  "int",
  "isinstance",
  "issubclass",
  "iter",
  "len",
  "list",
  "locals",
  "map",
  "max",
  "memoryview",
  "min",
  "next",
  "object",
  "oct",
  "open",
  "ord",
  "pow",
  "property",
  "range",
  "repr",
  "reversed",
  "round",
  "set",
  "setattr",
  "slice",
  "sorted",
  "staticmethod",
  "str",
  "sum",
  "super",
  "tuple",
  "type",
  "vars",
  "zip",
  "__import__",
  "NotImplemented",
  "Ellipsis",
  "__debug__"
];
function top(state) {
  return state.scopes[state.scopes.length - 1];
}
function mkPython(parserConf) {
  var ERRORCLASS2 = "error";
  var delimiters2 = parserConf.delimiters || parserConf.singleDelimiters || /^[\(\)\[\]\{\}@,:`=;\.\\]/;
  var operators4 = [
    parserConf.singleOperators,
    parserConf.doubleOperators,
    parserConf.doubleDelimiters,
    parserConf.tripleDelimiters,
    parserConf.operators || /^([-+*/%\/&|^]=?|[<>=]+|\/\/=?|\*\*=?|!=|[~!@]|\.\.\.)/
  ];
  for (var i = 0; i < operators4.length; i++) if (!operators4[i]) operators4.splice(i--, 1);
  var hangingIndent = parserConf.hangingIndent;
  var myKeywords = commonKeywords, myBuiltins = commonBuiltins;
  if (parserConf.extra_keywords != void 0)
    myKeywords = myKeywords.concat(parserConf.extra_keywords);
  if (parserConf.extra_builtins != void 0)
    myBuiltins = myBuiltins.concat(parserConf.extra_builtins);
  var py3 = !(parserConf.version && Number(parserConf.version) < 3);
  if (py3) {
    var identifiers5 = parserConf.identifiers || /^[_A-Za-z\u00A1-\uFFFF][_A-Za-z0-9\u00A1-\uFFFF]*/;
    myKeywords = myKeywords.concat(["nonlocal", "None", "aiter", "anext", "async", "await", "breakpoint", "match", "case"]);
    myBuiltins = myBuiltins.concat(["ascii", "bytes", "exec", "print"]);
    var stringPrefixes3 = new RegExp(`^(([rbuf]|(br)|(rb)|(fr)|(rf))?('{3}|"{3}|['"]))`, "i");
  } else {
    var identifiers5 = parserConf.identifiers || /^[_A-Za-z][_A-Za-z0-9]*/;
    myKeywords = myKeywords.concat(["exec", "print"]);
    myBuiltins = myBuiltins.concat([
      "apply",
      "basestring",
      "buffer",
      "cmp",
      "coerce",
      "execfile",
      "file",
      "intern",
      "long",
      "raw_input",
      "reduce",
      "reload",
      "unichr",
      "unicode",
      "xrange",
      "None"
    ]);
    var stringPrefixes3 = new RegExp(`^(([rubf]|(ur)|(br))?('{3}|"{3}|['"]))`, "i");
  }
  var keywords14 = wordRegexp3(myKeywords);
  var builtins7 = wordRegexp3(myBuiltins);
  function tokenBase13(stream, state) {
    var sol = stream.sol() && state.lastToken != "\\";
    if (sol) state.indent = stream.indentation();
    if (sol && top(state).type == "py") {
      var scopeOffset = top(state).offset;
      if (stream.eatSpace()) {
        var lineOffset = stream.indentation();
        if (lineOffset > scopeOffset)
          pushPyScope(stream, state);
        else if (lineOffset < scopeOffset && dedent2(stream, state) && stream.peek() != "#")
          state.errorToken = true;
        return null;
      } else {
        var style = tokenBaseInner(stream, state);
        if (scopeOffset > 0 && dedent2(stream, state))
          style += " " + ERRORCLASS2;
        return style;
      }
    }
    return tokenBaseInner(stream, state);
  }
  function tokenBaseInner(stream, state, inFormat) {
    if (stream.eatSpace()) return null;
    if (!inFormat && stream.match(/^#.*/)) return "comment";
    if (stream.match(/^[0-9\.]/, false)) {
      var floatLiteral = false;
      if (stream.match(/^[\d_]*\.\d+(e[\+\-]?\d+)?/i)) {
        floatLiteral = true;
      }
      if (stream.match(/^[\d_]+\.\d*/)) {
        floatLiteral = true;
      }
      if (stream.match(/^\.\d+/)) {
        floatLiteral = true;
      }
      if (floatLiteral) {
        stream.eat(/J/i);
        return "number";
      }
      var intLiteral = false;
      if (stream.match(/^0x[0-9a-f_]+/i)) intLiteral = true;
      if (stream.match(/^0b[01_]+/i)) intLiteral = true;
      if (stream.match(/^0o[0-7_]+/i)) intLiteral = true;
      if (stream.match(/^[1-9][\d_]*(e[\+\-]?[\d_]+)?/)) {
        stream.eat(/J/i);
        intLiteral = true;
      }
      if (stream.match(/^0(?![\dx])/i)) intLiteral = true;
      if (intLiteral) {
        stream.eat(/L/i);
        return "number";
      }
    }
    if (stream.match(stringPrefixes3)) {
      var isFmtString = stream.current().toLowerCase().indexOf("f") !== -1;
      if (!isFmtString) {
        state.tokenize = tokenStringFactory3(stream.current(), state.tokenize);
        return state.tokenize(stream, state);
      } else {
        state.tokenize = formatStringFactory(stream.current(), state.tokenize);
        return state.tokenize(stream, state);
      }
    }
    for (var i2 = 0; i2 < operators4.length; i2++)
      if (stream.match(operators4[i2])) return "operator";
    if (stream.match(delimiters2)) return "punctuation";
    if (state.lastToken == "." && stream.match(identifiers5))
      return "property";
    if (stream.match(keywords14) || stream.match(wordOperators2))
      return "keyword";
    if (stream.match(builtins7))
      return "builtin";
    if (stream.match(/^(self|cls)\b/))
      return "self";
    if (stream.match(identifiers5)) {
      if (state.lastToken == "def" || state.lastToken == "class")
        return "def";
      return "variable";
    }
    stream.next();
    return inFormat ? null : ERRORCLASS2;
  }
  function formatStringFactory(delimiter2, tokenOuter) {
    while ("rubf".indexOf(delimiter2.charAt(0).toLowerCase()) >= 0)
      delimiter2 = delimiter2.substr(1);
    var singleline = delimiter2.length == 1;
    var OUTCLASS = "string";
    function tokenNestedExpr(depth) {
      return function(stream, state) {
        var inner = tokenBaseInner(stream, state, true);
        if (inner == "punctuation") {
          if (stream.current() == "{") {
            state.tokenize = tokenNestedExpr(depth + 1);
          } else if (stream.current() == "}") {
            if (depth > 1) state.tokenize = tokenNestedExpr(depth - 1);
            else state.tokenize = tokenString8;
          }
        }
        return inner;
      };
    }
    function tokenString8(stream, state) {
      while (!stream.eol()) {
        stream.eatWhile(/[^'"\{\}\\]/);
        if (stream.eat("\\")) {
          stream.next();
          if (singleline && stream.eol())
            return OUTCLASS;
        } else if (stream.match(delimiter2)) {
          state.tokenize = tokenOuter;
          return OUTCLASS;
        } else if (stream.match("{{")) {
          return OUTCLASS;
        } else if (stream.match("{", false)) {
          state.tokenize = tokenNestedExpr(0);
          if (stream.current()) return OUTCLASS;
          else return state.tokenize(stream, state);
        } else if (stream.match("}}")) {
          return OUTCLASS;
        } else if (stream.match("}")) {
          return ERRORCLASS2;
        } else {
          stream.eat(/['"]/);
        }
      }
      if (singleline) {
        if (parserConf.singleLineStringErrors)
          return ERRORCLASS2;
        else
          state.tokenize = tokenOuter;
      }
      return OUTCLASS;
    }
    tokenString8.isString = true;
    return tokenString8;
  }
  function tokenStringFactory3(delimiter2, tokenOuter) {
    while ("rubf".indexOf(delimiter2.charAt(0).toLowerCase()) >= 0)
      delimiter2 = delimiter2.substr(1);
    var singleline = delimiter2.length == 1;
    var OUTCLASS = "string";
    function tokenString8(stream, state) {
      while (!stream.eol()) {
        stream.eatWhile(/[^'"\\]/);
        if (stream.eat("\\")) {
          stream.next();
          if (singleline && stream.eol())
            return OUTCLASS;
        } else if (stream.match(delimiter2)) {
          state.tokenize = tokenOuter;
          return OUTCLASS;
        } else {
          stream.eat(/['"]/);
        }
      }
      if (singleline) {
        if (parserConf.singleLineStringErrors)
          return ERRORCLASS2;
        else
          state.tokenize = tokenOuter;
      }
      return OUTCLASS;
    }
    tokenString8.isString = true;
    return tokenString8;
  }
  function pushPyScope(stream, state) {
    while (top(state).type != "py") state.scopes.pop();
    state.scopes.push({
      offset: top(state).offset + stream.indentUnit,
      type: "py",
      align: null
    });
  }
  function pushBracketScope(stream, state, type2) {
    var align = stream.match(/^[\s\[\{\(]*(?:#|$)/, false) ? null : stream.column() + 1;
    state.scopes.push({
      offset: state.indent + (hangingIndent || stream.indentUnit),
      type: type2,
      align
    });
  }
  function dedent2(stream, state) {
    var indented = stream.indentation();
    while (state.scopes.length > 1 && top(state).offset > indented) {
      if (top(state).type != "py") return true;
      state.scopes.pop();
    }
    return top(state).offset != indented;
  }
  function tokenLexer2(stream, state) {
    if (stream.sol()) {
      state.beginningOfLine = true;
      state.dedent = false;
    }
    var style = state.tokenize(stream, state);
    var current = stream.current();
    if (state.beginningOfLine && current == "@")
      return stream.match(identifiers5, false) ? "meta" : py3 ? "operator" : ERRORCLASS2;
    if (/\S/.test(current)) state.beginningOfLine = false;
    if ((style == "variable" || style == "builtin") && state.lastToken == "meta")
      style = "meta";
    if (current == "pass" || current == "return")
      state.dedent = true;
    if (current == "lambda") state.lambda = true;
    if (current == ":" && !state.lambda && top(state).type == "py" && stream.match(/^\s*(?:#|$)/, false))
      pushPyScope(stream, state);
    if (current.length == 1 && !/string|comment/.test(style)) {
      var delimiter_index = "[({".indexOf(current);
      if (delimiter_index != -1)
        pushBracketScope(stream, state, "])}".slice(delimiter_index, delimiter_index + 1));
      delimiter_index = "])}".indexOf(current);
      if (delimiter_index != -1) {
        if (top(state).type == current) state.indent = state.scopes.pop().offset - (hangingIndent || stream.indentUnit);
        else return ERRORCLASS2;
      }
    }
    if (state.dedent && stream.eol() && top(state).type == "py" && state.scopes.length > 1)
      state.scopes.pop();
    return style;
  }
  return {
    name: "python",
    startState: function() {
      return {
        tokenize: tokenBase13,
        scopes: [{ offset: 0, type: "py", align: null }],
        indent: 0,
        lastToken: null,
        lambda: false,
        dedent: 0
      };
    },
    token: function(stream, state) {
      var addErr = state.errorToken;
      if (addErr) state.errorToken = false;
      var style = tokenLexer2(stream, state);
      if (style && style != "comment")
        state.lastToken = style == "keyword" || style == "punctuation" ? stream.current() : style;
      if (style == "punctuation") style = null;
      if (stream.eol() && state.lambda)
        state.lambda = false;
      return addErr ? ERRORCLASS2 : style;
    },
    indent: function(state, textAfter, cx) {
      if (state.tokenize != tokenBase13)
        return state.tokenize.isString ? null : 0;
      var scope = top(state);
      var closing3 = scope.type == textAfter.charAt(0) || scope.type == "py" && !state.dedent && /^(else:|elif |except |finally:)/.test(textAfter);
      if (scope.align != null)
        return scope.align - (closing3 ? 1 : 0);
      else
        return scope.offset - (closing3 ? hangingIndent || cx.unit : 0);
    },
    languageData: {
      autocomplete: commonKeywords.concat(commonBuiltins).concat(["exec", "print"]),
      indentOnInput: /^\s*([\}\]\)]|else:|elif |except |finally:)$/,
      commentTokens: { line: "#" },
      closeBrackets: { brackets: ["(", "[", "{", "'", '"', "'''", '"""'] }
    }
  };
}
var words5 = function(str) {
  return str.split(" ");
};
var python = mkPython({});
var cython = mkPython({
  extra_keywords: words5("by cdef cimport cpdef ctypedef enum except extern gil include nogil property public readonly struct union DEF IF ELIF ELSE")
});

// node_modules/@codemirror/legacy-modes/mode/r.js
function wordObj(words8) {
  var res = {};
  for (var i = 0; i < words8.length; ++i) res[words8[i]] = true;
  return res;
}
var commonAtoms = ["NULL", "NA", "Inf", "NaN", "NA_integer_", "NA_real_", "NA_complex_", "NA_character_", "TRUE", "FALSE"];
var commonBuiltins2 = ["list", "quote", "bquote", "eval", "return", "call", "parse", "deparse"];
var commonKeywords2 = ["if", "else", "repeat", "while", "function", "for", "in", "next", "break"];
var commonBlockKeywords = ["if", "else", "repeat", "while", "function", "for"];
var atoms4 = wordObj(commonAtoms);
var builtins6 = wordObj(commonBuiltins2);
var keywords9 = wordObj(commonKeywords2);
var blockkeywords = wordObj(commonBlockKeywords);
var opChars = /[+\-*\/^<>=!&|~$:]/;
var curPunc2;
function tokenBase8(stream, state) {
  curPunc2 = null;
  var ch = stream.next();
  if (ch == "#") {
    stream.skipToEnd();
    return "comment";
  } else if (ch == "0" && stream.eat("x")) {
    stream.eatWhile(/[\da-f]/i);
    return "number";
  } else if (ch == "." && stream.eat(/\d/)) {
    stream.match(/\d*(?:e[+\-]?\d+)?/);
    return "number";
  } else if (/\d/.test(ch)) {
    stream.match(/\d*(?:\.\d+)?(?:e[+\-]\d+)?L?/);
    return "number";
  } else if (ch == "'" || ch == '"') {
    state.tokenize = tokenString5(ch);
    return "string";
  } else if (ch == "`") {
    stream.match(/[^`]+`/);
    return "string.special";
  } else if (ch == "." && stream.match(/.(?:[.]|\d+)/)) {
    return "keyword";
  } else if (/[a-zA-Z\.]/.test(ch)) {
    stream.eatWhile(/[\w\.]/);
    var word = stream.current();
    if (atoms4.propertyIsEnumerable(word)) return "atom";
    if (keywords9.propertyIsEnumerable(word)) {
      if (blockkeywords.propertyIsEnumerable(word) && !stream.match(/\s*if(\s+|$)/, false))
        curPunc2 = "block";
      return "keyword";
    }
    if (builtins6.propertyIsEnumerable(word)) return "builtin";
    return "variable";
  } else if (ch == "%") {
    if (stream.skipTo("%")) stream.next();
    return "variableName.special";
  } else if (ch == "<" && stream.eat("-") || ch == "<" && stream.match("<-") || ch == "-" && stream.match(/>>?/)) {
    return "operator";
  } else if (ch == "=" && state.ctx.argList) {
    return "operator";
  } else if (opChars.test(ch)) {
    if (ch == "$") return "operator";
    stream.eatWhile(opChars);
    return "operator";
  } else if (/[\(\){}\[\];]/.test(ch)) {
    curPunc2 = ch;
    if (ch == ";") return "punctuation";
    return null;
  } else {
    return null;
  }
}
function tokenString5(quote) {
  return function(stream, state) {
    if (stream.eat("\\")) {
      var ch = stream.next();
      if (ch == "x") stream.match(/^[a-f0-9]{2}/i);
      else if ((ch == "u" || ch == "U") && stream.eat("{") && stream.skipTo("}")) stream.next();
      else if (ch == "u") stream.match(/^[a-f0-9]{4}/i);
      else if (ch == "U") stream.match(/^[a-f0-9]{8}/i);
      else if (/[0-7]/.test(ch)) stream.match(/^[0-7]{1,2}/);
      return "string.special";
    } else {
      var next;
      while ((next = stream.next()) != null) {
        if (next == quote) {
          state.tokenize = tokenBase8;
          break;
        }
        if (next == "\\") {
          stream.backUp(1);
          break;
        }
      }
      return "string";
    }
  };
}
var ALIGN_YES = 1;
var ALIGN_NO = 2;
var BRACELESS = 4;
function push(state, type2, stream) {
  state.ctx = {
    type: type2,
    indent: state.indent,
    flags: 0,
    column: stream.column(),
    prev: state.ctx
  };
}
function setFlag(state, flag) {
  var ctx = state.ctx;
  state.ctx = {
    type: ctx.type,
    indent: ctx.indent,
    flags: ctx.flags | flag,
    column: ctx.column,
    prev: ctx.prev
  };
}
function pop(state) {
  state.indent = state.ctx.indent;
  state.ctx = state.ctx.prev;
}
var r = {
  name: "r",
  startState: function(indentUnit) {
    return {
      tokenize: tokenBase8,
      ctx: {
        type: "top",
        indent: -indentUnit,
        flags: ALIGN_NO
      },
      indent: 0,
      afterIdent: false
    };
  },
  token: function(stream, state) {
    if (stream.sol()) {
      if ((state.ctx.flags & 3) == 0) state.ctx.flags |= ALIGN_NO;
      if (state.ctx.flags & BRACELESS) pop(state);
      state.indent = stream.indentation();
    }
    if (stream.eatSpace()) return null;
    var style = state.tokenize(stream, state);
    if (style != "comment" && (state.ctx.flags & ALIGN_NO) == 0) setFlag(state, ALIGN_YES);
    if ((curPunc2 == ";" || curPunc2 == "{" || curPunc2 == "}") && state.ctx.type == "block") pop(state);
    if (curPunc2 == "{") push(state, "}", stream);
    else if (curPunc2 == "(") {
      push(state, ")", stream);
      if (state.afterIdent) state.ctx.argList = true;
    } else if (curPunc2 == "[") push(state, "]", stream);
    else if (curPunc2 == "block") push(state, "block", stream);
    else if (curPunc2 == state.ctx.type) pop(state);
    else if (state.ctx.type == "block" && style != "comment") setFlag(state, BRACELESS);
    state.afterIdent = style == "variable" || style == "keyword";
    return style;
  },
  indent: function(state, textAfter, cx) {
    if (state.tokenize != tokenBase8) return 0;
    var firstChar = textAfter && textAfter.charAt(0), ctx = state.ctx, closing3 = firstChar == ctx.type;
    if (ctx.flags & BRACELESS) ctx = ctx.prev;
    if (ctx.type == "block") return ctx.indent + (firstChar == "{" ? 0 : cx.unit);
    else if (ctx.flags & ALIGN_YES) return ctx.column + (closing3 ? 0 : 1);
    else return ctx.indent + (closing3 ? 0 : cx.unit);
  },
  languageData: {
    wordChars: ".",
    commentTokens: { line: "#" },
    autocomplete: commonAtoms.concat(commonBuiltins2, commonKeywords2)
  }
};

// node_modules/@codemirror/legacy-modes/mode/ruby.js
function wordObj2(words8) {
  var o = {};
  for (var i = 0, e = words8.length; i < e; ++i) o[words8[i]] = true;
  return o;
}
var keywordList = [
  "alias",
  "and",
  "BEGIN",
  "begin",
  "break",
  "case",
  "class",
  "def",
  "defined?",
  "do",
  "else",
  "elsif",
  "END",
  "end",
  "ensure",
  "false",
  "for",
  "if",
  "in",
  "module",
  "next",
  "not",
  "or",
  "redo",
  "rescue",
  "retry",
  "return",
  "self",
  "super",
  "then",
  "true",
  "undef",
  "unless",
  "until",
  "when",
  "while",
  "yield",
  "nil",
  "raise",
  "throw",
  "catch",
  "fail",
  "loop",
  "callcc",
  "caller",
  "lambda",
  "proc",
  "public",
  "protected",
  "private",
  "require",
  "load",
  "require_relative",
  "extend",
  "autoload",
  "__END__",
  "__FILE__",
  "__LINE__",
  "__dir__"
];
var keywords10 = wordObj2(keywordList);
var indentWords = wordObj2([
  "def",
  "class",
  "case",
  "for",
  "while",
  "until",
  "module",
  "catch",
  "loop",
  "proc",
  "begin"
]);
var dedentWords = wordObj2(["end", "until"]);
var opening = { "[": "]", "{": "}", "(": ")" };
var closing = { "]": "[", "}": "{", ")": "(" };
var curPunc3;
function chain(newtok, stream, state) {
  state.tokenize.push(newtok);
  return newtok(stream, state);
}
function tokenBase9(stream, state) {
  if (stream.sol() && stream.match("=begin") && stream.eol()) {
    state.tokenize.push(readBlockComment);
    return "comment";
  }
  if (stream.eatSpace()) return null;
  var ch = stream.next(), m;
  if (ch == "`" || ch == "'" || ch == '"') {
    return chain(readQuoted(ch, "string", ch == '"' || ch == "`"), stream, state);
  } else if (ch == "/") {
    if (regexpAhead(stream))
      return chain(readQuoted(ch, "string.special", true), stream, state);
    else
      return "operator";
  } else if (ch == "%") {
    var style = "string", embed = true;
    if (stream.eat("s")) style = "atom";
    else if (stream.eat(/[WQ]/)) style = "string";
    else if (stream.eat(/[r]/)) style = "string.special";
    else if (stream.eat(/[wxq]/)) {
      style = "string";
      embed = false;
    }
    var delim = stream.eat(/[^\w\s=]/);
    if (!delim) return "operator";
    if (opening.propertyIsEnumerable(delim)) delim = opening[delim];
    return chain(readQuoted(delim, style, embed, true), stream, state);
  } else if (ch == "#") {
    stream.skipToEnd();
    return "comment";
  } else if (ch == "<" && (m = stream.match(/^<([-~])[\`\"\']?([a-zA-Z_?]\w*)[\`\"\']?(?:;|$)/))) {
    return chain(readHereDoc(m[2], m[1]), stream, state);
  } else if (ch == "0") {
    if (stream.eat("x")) stream.eatWhile(/[\da-fA-F]/);
    else if (stream.eat("b")) stream.eatWhile(/[01]/);
    else stream.eatWhile(/[0-7]/);
    return "number";
  } else if (/\d/.test(ch)) {
    stream.match(/^[\d_]*(?:\.[\d_]+)?(?:[eE][+\-]?[\d_]+)?/);
    return "number";
  } else if (ch == "?") {
    while (stream.match(/^\\[CM]-/)) {
    }
    if (stream.eat("\\")) stream.eatWhile(/\w/);
    else stream.next();
    return "string";
  } else if (ch == ":") {
    if (stream.eat("'")) return chain(readQuoted("'", "atom", false), stream, state);
    if (stream.eat('"')) return chain(readQuoted('"', "atom", true), stream, state);
    if (stream.eat(/[\<\>]/)) {
      stream.eat(/[\<\>]/);
      return "atom";
    }
    if (stream.eat(/[\+\-\*\/\&\|\:\!]/)) {
      return "atom";
    }
    if (stream.eat(/[a-zA-Z$@_\xa1-\uffff]/)) {
      stream.eatWhile(/[\w$\xa1-\uffff]/);
      stream.eat(/[\?\!\=]/);
      return "atom";
    }
    return "operator";
  } else if (ch == "@" && stream.match(/^@?[a-zA-Z_\xa1-\uffff]/)) {
    stream.eat("@");
    stream.eatWhile(/[\w\xa1-\uffff]/);
    return "propertyName";
  } else if (ch == "$") {
    if (stream.eat(/[a-zA-Z_]/)) {
      stream.eatWhile(/[\w]/);
    } else if (stream.eat(/\d/)) {
      stream.eat(/\d/);
    } else {
      stream.next();
    }
    return "variableName.special";
  } else if (/[a-zA-Z_\xa1-\uffff]/.test(ch)) {
    stream.eatWhile(/[\w\xa1-\uffff]/);
    stream.eat(/[\?\!]/);
    if (stream.eat(":")) return "atom";
    return "variable";
  } else if (ch == "|" && (state.varList || state.lastTok == "{" || state.lastTok == "do")) {
    curPunc3 = "|";
    return null;
  } else if (/[\(\)\[\]{}\\;]/.test(ch)) {
    curPunc3 = ch;
    return null;
  } else if (ch == "-" && stream.eat(">")) {
    return "operator";
  } else if (/[=+\-\/*:\.^%<>~|]/.test(ch)) {
    var more = stream.eatWhile(/[=+\-\/*:\.^%<>~|]/);
    if (ch == "." && !more) curPunc3 = ".";
    return "operator";
  } else {
    return null;
  }
}
function regexpAhead(stream) {
  var start = stream.pos, depth = 0, next, found = false, escaped = false;
  while ((next = stream.next()) != null) {
    if (!escaped) {
      if ("[{(".indexOf(next) > -1) {
        depth++;
      } else if ("]})".indexOf(next) > -1) {
        depth--;
        if (depth < 0) break;
      } else if (next == "/" && depth == 0) {
        found = true;
        break;
      }
      escaped = next == "\\";
    } else {
      escaped = false;
    }
  }
  stream.backUp(stream.pos - start);
  return found;
}
function tokenBaseUntilBrace(depth) {
  if (!depth) depth = 1;
  return function(stream, state) {
    if (stream.peek() == "}") {
      if (depth == 1) {
        state.tokenize.pop();
        return state.tokenize[state.tokenize.length - 1](stream, state);
      } else {
        state.tokenize[state.tokenize.length - 1] = tokenBaseUntilBrace(depth - 1);
      }
    } else if (stream.peek() == "{") {
      state.tokenize[state.tokenize.length - 1] = tokenBaseUntilBrace(depth + 1);
    }
    return tokenBase9(stream, state);
  };
}
function tokenBaseOnce() {
  var alreadyCalled = false;
  return function(stream, state) {
    if (alreadyCalled) {
      state.tokenize.pop();
      return state.tokenize[state.tokenize.length - 1](stream, state);
    }
    alreadyCalled = true;
    return tokenBase9(stream, state);
  };
}
function readQuoted(quote, style, embed, unescaped) {
  return function(stream, state) {
    var escaped = false, ch;
    if (state.context.type === "read-quoted-paused") {
      state.context = state.context.prev;
      stream.eat("}");
    }
    while ((ch = stream.next()) != null) {
      if (ch == quote && (unescaped || !escaped)) {
        state.tokenize.pop();
        break;
      }
      if (embed && ch == "#" && !escaped) {
        if (stream.eat("{")) {
          if (quote == "}") {
            state.context = { prev: state.context, type: "read-quoted-paused" };
          }
          state.tokenize.push(tokenBaseUntilBrace());
          break;
        } else if (/[@\$]/.test(stream.peek())) {
          state.tokenize.push(tokenBaseOnce());
          break;
        }
      }
      escaped = !escaped && ch == "\\";
    }
    return style;
  };
}
function readHereDoc(phrase, mayIndent) {
  return function(stream, state) {
    if (mayIndent) stream.eatSpace();
    if (stream.match(phrase)) state.tokenize.pop();
    else stream.skipToEnd();
    return "string";
  };
}
function readBlockComment(stream, state) {
  if (stream.sol() && stream.match("=end") && stream.eol())
    state.tokenize.pop();
  stream.skipToEnd();
  return "comment";
}
var ruby = {
  name: "ruby",
  startState: function(indentUnit) {
    return {
      tokenize: [tokenBase9],
      indented: 0,
      context: { type: "top", indented: -indentUnit },
      continuedLine: false,
      lastTok: null,
      varList: false
    };
  },
  token: function(stream, state) {
    curPunc3 = null;
    if (stream.sol()) state.indented = stream.indentation();
    var style = state.tokenize[state.tokenize.length - 1](stream, state), kwtype;
    var thisTok = curPunc3;
    if (style == "variable") {
      var word = stream.current();
      style = state.lastTok == "." ? "property" : keywords10.propertyIsEnumerable(stream.current()) ? "keyword" : /^[A-Z]/.test(word) ? "tag" : state.lastTok == "def" || state.lastTok == "class" || state.varList ? "def" : "variable";
      if (style == "keyword") {
        thisTok = word;
        if (indentWords.propertyIsEnumerable(word)) kwtype = "indent";
        else if (dedentWords.propertyIsEnumerable(word)) kwtype = "dedent";
        else if ((word == "if" || word == "unless") && stream.column() == stream.indentation())
          kwtype = "indent";
        else if (word == "do" && state.context.indented < state.indented)
          kwtype = "indent";
      }
    }
    if (curPunc3 || style && style != "comment") state.lastTok = thisTok;
    if (curPunc3 == "|") state.varList = !state.varList;
    if (kwtype == "indent" || /[\(\[\{]/.test(curPunc3))
      state.context = { prev: state.context, type: curPunc3 || style, indented: state.indented };
    else if ((kwtype == "dedent" || /[\)\]\}]/.test(curPunc3)) && state.context.prev)
      state.context = state.context.prev;
    if (stream.eol())
      state.continuedLine = curPunc3 == "\\" || style == "operator";
    return style;
  },
  indent: function(state, textAfter, cx) {
    if (state.tokenize[state.tokenize.length - 1] != tokenBase9) return null;
    var firstChar = textAfter && textAfter.charAt(0);
    var ct = state.context;
    var closed = ct.type == closing[firstChar] || ct.type == "keyword" && /^(?:end|until|else|elsif|when|rescue)\b/.test(textAfter);
    return ct.indented + (closed ? 0 : cx.unit) + (state.continuedLine ? cx.unit : 0);
  },
  languageData: {
    indentOnInput: /^\s*(?:end|rescue|elsif|else|\})$/,
    commentTokens: { line: "#" },
    autocomplete: keywordList
  }
};

// node_modules/@codemirror/legacy-modes/mode/simple-mode.js
function simpleMode(states) {
  ensureState(states, "start");
  var states_ = {}, meta = states.languageData || {}, hasIndentation = false;
  for (var state in states) if (state != meta && states.hasOwnProperty(state)) {
    var list = states_[state] = [], orig = states[state];
    for (var i = 0; i < orig.length; i++) {
      var data = orig[i];
      list.push(new Rule(data, states));
      if (data.indent || data.dedent) hasIndentation = true;
    }
  }
  return {
    name: meta.name,
    startState: function() {
      return { state: "start", pending: null, indent: hasIndentation ? [] : null };
    },
    copyState: function(state2) {
      var s = { state: state2.state, pending: state2.pending, indent: state2.indent && state2.indent.slice(0) };
      if (state2.stack)
        s.stack = state2.stack.slice(0);
      return s;
    },
    token: tokenFunction(states_),
    indent: indentFunction(states_, meta),
    mergeTokens: meta.mergeTokens,
    languageData: meta
  };
}
function ensureState(states, name) {
  if (!states.hasOwnProperty(name))
    throw new Error("Undefined state " + name + " in simple mode");
}
function toRegex(val, caret) {
  if (!val) return /(?:)/;
  var flags = "";
  if (val instanceof RegExp) {
    if (val.ignoreCase) flags = "i";
    val = val.source;
  } else {
    val = String(val);
  }
  return new RegExp((caret === false ? "" : "^") + "(?:" + val + ")", flags);
}
function asToken(val) {
  if (!val) return null;
  if (val.apply) return val;
  if (typeof val == "string") return val.replace(/\./g, " ");
  var result = [];
  for (var i = 0; i < val.length; i++)
    result.push(val[i] && val[i].replace(/\./g, " "));
  return result;
}
function Rule(data, states) {
  if (data.next || data.push) ensureState(states, data.next || data.push);
  this.regex = toRegex(data.regex);
  this.token = asToken(data.token);
  this.data = data;
}
function tokenFunction(states) {
  return function(stream, state) {
    if (state.pending) {
      var pend = state.pending.shift();
      if (state.pending.length == 0) state.pending = null;
      stream.pos += pend.text.length;
      return pend.token;
    }
    var curState = states[state.state];
    for (var i = 0; i < curState.length; i++) {
      var rule = curState[i];
      var matches = (!rule.data.sol || stream.sol()) && stream.match(rule.regex);
      if (matches) {
        if (rule.data.next) {
          state.state = rule.data.next;
        } else if (rule.data.push) {
          (state.stack || (state.stack = [])).push(state.state);
          state.state = rule.data.push;
        } else if (rule.data.pop && state.stack && state.stack.length) {
          state.state = state.stack.pop();
        }
        if (rule.data.indent)
          state.indent.push(stream.indentation() + stream.indentUnit);
        if (rule.data.dedent)
          state.indent.pop();
        var token = rule.token;
        if (token && token.apply) token = token(matches);
        if (matches.length > 2 && rule.token && typeof rule.token != "string") {
          state.pending = [];
          for (var j = 2; j < matches.length; j++)
            if (matches[j])
              state.pending.push({ text: matches[j], token: rule.token[j - 1] });
          stream.backUp(matches[0].length - (matches[1] ? matches[1].length : 0));
          return token[0];
        } else if (token && token.join) {
          return token[0];
        } else {
          return token;
        }
      }
    }
    stream.next();
    return null;
  };
}
function indentFunction(states, meta) {
  return function(state, textAfter) {
    if (state.indent == null || meta.dontIndentStates && meta.dontIndentStates.indexOf(state.state) > -1)
      return null;
    var pos = state.indent.length - 1, rules = states[state.state];
    scan: for (; ; ) {
      for (var i = 0; i < rules.length; i++) {
        var rule = rules[i];
        if (rule.data.dedent && rule.data.dedentIfLineStart !== false) {
          var m = rule.regex.exec(textAfter);
          if (m && m[0]) {
            pos--;
            if (rule.next || rule.push) rules = states[rule.next || rule.push];
            textAfter = textAfter.slice(m[0].length);
            continue scan;
          }
        }
      }
      break;
    }
    return pos < 0 ? 0 : state.indent[pos];
  };
}

// node_modules/@codemirror/legacy-modes/mode/rust.js
var rust = simpleMode({
  start: [
    // string and byte string
    { regex: /b?"/, token: "string", next: "string" },
    // raw string and raw byte string
    { regex: /b?r"/, token: "string", next: "string_raw" },
    { regex: /b?r#+"/, token: "string", next: "string_raw_hash" },
    // character
    { regex: /'(?:[^'\\]|\\(?:[nrt0'"]|x[\da-fA-F]{2}|u\{[\da-fA-F]{6}\}))'/, token: "string.special" },
    // byte
    { regex: /b'(?:[^']|\\(?:['\\nrt0]|x[\da-fA-F]{2}))'/, token: "string.special" },
    {
      regex: /(?:(?:[0-9][0-9_]*)(?:(?:[Ee][+-]?[0-9_]+)|\.[0-9_]+(?:[Ee][+-]?[0-9_]+)?)(?:f32|f64)?)|(?:0(?:b[01_]+|(?:o[0-7_]+)|(?:x[0-9a-fA-F_]+))|(?:[0-9][0-9_]*))(?:u8|u16|u32|u64|i8|i16|i32|i64|isize|usize)?/,
      token: "number"
    },
    { regex: /(let(?:\s+mut)?|fn|enum|mod|struct|type|union)(\s+)([a-zA-Z_][a-zA-Z0-9_]*)/, token: ["keyword", null, "def"] },
    { regex: /(?:abstract|alignof|as|async|await|box|break|continue|const|crate|do|dyn|else|enum|extern|fn|for|final|if|impl|in|loop|macro|match|mod|move|offsetof|override|priv|proc|pub|pure|ref|return|self|sizeof|static|struct|super|trait|type|typeof|union|unsafe|unsized|use|virtual|where|while|yield)\b/, token: "keyword" },
    { regex: /\b(?:Self|isize|usize|char|bool|u8|u16|u32|u64|f16|f32|f64|i8|i16|i32|i64|str|Option)\b/, token: "atom" },
    { regex: /\b(?:true|false|Some|None|Ok|Err)\b/, token: "builtin" },
    {
      regex: /\b(fn)(\s+)([a-zA-Z_][a-zA-Z0-9_]*)/,
      token: ["keyword", null, "def"]
    },
    { regex: /#!?\[.*\]/, token: "meta" },
    { regex: /\/\/.*/, token: "comment" },
    { regex: /\/\*/, token: "comment", next: "comment" },
    { regex: /[-+\/*=<>!]+/, token: "operator" },
    { regex: /[a-zA-Z_]\w*!/, token: "macroName" },
    { regex: /[a-zA-Z_]\w*/, token: "variable" },
    { regex: /[\{\[\(]/, indent: true },
    { regex: /[\}\]\)]/, dedent: true }
  ],
  string: [
    { regex: /"/, token: "string", next: "start" },
    { regex: /(?:[^\\"]|\\(?:.|$))*/, token: "string" }
  ],
  string_raw: [
    { regex: /"/, token: "string", next: "start" },
    { regex: /[^"]*/, token: "string" }
  ],
  string_raw_hash: [
    { regex: /"#+/, token: "string", next: "start" },
    { regex: /(?:[^"]|"(?!#))*/, token: "string" }
  ],
  comment: [
    { regex: /.*?\*\//, token: "comment", next: "start" },
    { regex: /.*/, token: "comment" }
  ],
  languageData: {
    name: "rust",
    dontIndentStates: ["comment"],
    indentOnInput: /^\s*\}$/,
    commentTokens: { line: "//", block: { open: "/*", close: "*/" } }
  }
});

// node_modules/@codemirror/legacy-modes/mode/sas.js
var words6 = {};
var isDoubleOperatorSym = {
  eq: "operator",
  lt: "operator",
  le: "operator",
  gt: "operator",
  ge: "operator",
  "in": "operator",
  ne: "operator",
  or: "operator"
};
var isDoubleOperatorChar = /(<=|>=|!=|<>)/;
var isSingleOperatorChar = /[=\(:\),{}.*<>+\-\/^\[\]]/;
function define(style, string2, context) {
  if (context) {
    var split = string2.split(" ");
    for (var i = 0; i < split.length; i++) {
      words6[split[i]] = { style, state: context };
    }
  }
}
define("def", "stack pgm view source debug nesting nolist", ["inDataStep"]);
define("def", "if while until for do do; end end; then else cancel", ["inDataStep"]);
define("def", "label format _n_ _error_", ["inDataStep"]);
define("def", "ALTER BUFNO BUFSIZE CNTLLEV COMPRESS DLDMGACTION ENCRYPT ENCRYPTKEY EXTENDOBSCOUNTER GENMAX GENNUM INDEX LABEL OBSBUF OUTREP PW PWREQ READ REPEMPTY REPLACE REUSE ROLE SORTEDBY SPILL TOBSNO TYPE WRITE FILECLOSE FIRSTOBS IN OBS POINTOBS WHERE WHEREUP IDXNAME IDXWHERE DROP KEEP RENAME", ["inDataStep"]);
define("def", "filevar finfo finv fipname fipnamel fipstate first firstobs floor", ["inDataStep"]);
define("def", "varfmt varinfmt varlabel varlen varname varnum varray varrayx vartype verify vformat vformatd vformatdx vformatn vformatnx vformatw vformatwx vformatx vinarray vinarrayx vinformat vinformatd vinformatdx vinformatn vinformatnx vinformatw vinformatwx vinformatx vlabel vlabelx vlength vlengthx vname vnamex vnferr vtype vtypex weekday", ["inDataStep"]);
define("def", "zipfips zipname zipnamel zipstate", ["inDataStep"]);
define("def", "put putc putn", ["inDataStep"]);
define("builtin", "data run", ["inDataStep"]);
define("def", "data", ["inProc"]);
define("def", "%if %end %end; %else %else; %do %do; %then", ["inMacro"]);
define("builtin", "proc run; quit; libname filename %macro %mend option options", ["ALL"]);
define("def", "footnote title libname ods", ["ALL"]);
define("def", "%let %put %global %sysfunc %eval ", ["ALL"]);
define("variable", "&sysbuffr &syscc &syscharwidth &syscmd &sysdate &sysdate9 &sysday &sysdevic &sysdmg &sysdsn &sysencoding &sysenv &syserr &syserrortext &sysfilrc &syshostname &sysindex &sysinfo &sysjobid &syslast &syslckrc &syslibrc &syslogapplname &sysmacroname &sysmenv &sysmsg &sysncpu &sysodspath &sysparm &syspbuff &sysprocessid &sysprocessname &sysprocname &sysrc &sysscp &sysscpl &sysscpl &syssite &sysstartid &sysstartname &systcpiphostname &systime &sysuserid &sysver &sysvlong &sysvlong4 &syswarningtext", ["ALL"]);
define("def", "source2 nosource2 page pageno pagesize", ["ALL"]);
define("def", "_all_ _character_ _cmd_ _freq_ _i_ _infile_ _last_ _msg_ _null_ _numeric_ _temporary_ _type_ abort abs addr adjrsq airy alpha alter altlog altprint and arcos array arsin as atan attrc attrib attrn authserver autoexec awscontrol awsdef awsmenu awsmenumerge awstitle backward band base betainv between blocksize blshift bnot bor brshift bufno bufsize bxor by byerr byline byte calculated call cards cards4 catcache cbufno cdf ceil center cexist change chisq cinv class cleanup close cnonct cntllev coalesce codegen col collate collin column comamid comaux1 comaux2 comdef compbl compound compress config continue convert cos cosh cpuid create cross crosstab css curobs cv daccdb daccdbsl daccsl daccsyd dacctab dairy datalines datalines4 datejul datepart datetime day dbcslang dbcstype dclose ddfm ddm delete delimiter depdb depdbsl depsl depsyd deptab dequote descending descript design= device dflang dhms dif digamma dim dinfo display distinct dkricond dkrocond dlm dnum do dopen doptname doptnum dread drop dropnote dsname dsnferr echo else emaildlg emailid emailpw emailserver emailsys encrypt end endsas engine eof eov erf erfc error errorcheck errors exist exp fappend fclose fcol fdelete feedback fetch fetchobs fexist fget file fileclose fileexist filefmt filename fileref  fmterr fmtsearch fnonct fnote font fontalias  fopen foptname foptnum force formatted formchar formdelim formdlim forward fpoint fpos fput fread frewind frlen from fsep fuzz fwrite gaminv gamma getoption getvarc getvarn go goto group gwindow hbar hbound helpenv helploc hms honorappearance hosthelp hostprint hour hpct html hvar ibessel ibr id if index indexc indexw initcmd initstmt inner input inputc inputn inr insert int intck intnx into intrr invaliddata irr is jbessel join juldate keep kentb kurtosis label lag last lbound leave left length levels lgamma lib  library libref line linesize link list log log10 log2 logpdf logpmf logsdf lostcard lowcase lrecl ls macro macrogen maps mautosource max maxdec maxr mdy mean measures median memtype merge merror min minute missing missover mlogic mod mode model modify month mopen mort mprint mrecall msglevel msymtabmax mvarsize myy n nest netpv new news nmiss no nobatch nobs nocaps nocardimage nocenter nocharcode nocmdmac nocol nocum nodate nodbcs nodetails nodmr nodms nodmsbatch nodup nodupkey noduplicates noechoauto noequals noerrorabend noexitwindows nofullstimer noicon noimplmac noint nolist noloadlist nomiss nomlogic nomprint nomrecall nomsgcase nomstored nomultenvappl nonotes nonumber noobs noovp nopad nopercent noprint noprintinit normal norow norsasuser nosetinit  nosplash nosymbolgen note notes notitle notitles notsorted noverbose noxsync noxwait npv null number numkeys nummousekeys nway obs  on open     order ordinal otherwise out outer outp= output over ovp p(1 5 10 25 50 75 90 95 99) pad pad2  paired parm parmcards path pathdll pathname pdf peek peekc pfkey pmf point poisson poke position printer probbeta probbnml probchi probf probgam probhypr probit probnegb probnorm probsig probt procleave prt ps  pw pwreq qtr quote r ranbin rancau random ranexp rangam range ranks rannor ranpoi rantbl rantri ranuni rcorr read recfm register regr remote remove rename repeat repeated replace resolve retain return reuse reverse rewind right round rsquare rtf rtrace rtraceloc s s2 samploc sasautos sascontrol sasfrscr sasmsg sasmstore sasscript sasuser saving scan sdf second select selection separated seq serror set setcomm setot sign simple sin sinh siteinfo skewness skip sle sls sortedby sortpgm sortseq sortsize soundex  spedis splashlocation split spool sqrt start std stderr stdin stfips stimer stname stnamel stop stopover sub subgroup subpopn substr sum sumwgt symbol symbolgen symget symput sysget sysin sysleave sysmsg sysparm sysprint sysprintfont sysprod sysrc system t table tables tan tanh tapeclose tbufsize terminal test then timepart tinv  tnonct to today tol tooldef totper transformout translate trantab tranwrd trigamma trim trimn trunc truncover type unformatted uniform union until upcase update user usericon uss validate value var  weight when where while wincharset window work workinit workterm write wsum xsync xwait yearcutoff yes yyq  min max", ["inDataStep", "inProc"]);
define("operator", "and not ", ["inDataStep", "inProc"]);
function tokenize(stream, state) {
  var ch = stream.next();
  if (ch === "/" && stream.eat("*")) {
    state.continueComment = true;
    return "comment";
  } else if (state.continueComment === true) {
    if (ch === "*" && stream.peek() === "/") {
      stream.next();
      state.continueComment = false;
    } else if (stream.skipTo("*")) {
      stream.skipTo("*");
      stream.next();
      if (stream.eat("/"))
        state.continueComment = false;
    } else {
      stream.skipToEnd();
    }
    return "comment";
  }
  if (ch == "*" && stream.column() == stream.indentation()) {
    stream.skipToEnd();
    return "comment";
  }
  var doubleOperator = ch + stream.peek();
  if ((ch === '"' || ch === "'") && !state.continueString) {
    state.continueString = ch;
    return "string";
  } else if (state.continueString) {
    if (state.continueString == ch) {
      state.continueString = null;
    } else if (stream.skipTo(state.continueString)) {
      stream.next();
      state.continueString = null;
    } else {
      stream.skipToEnd();
    }
    return "string";
  } else if (state.continueString !== null && stream.eol()) {
    stream.skipTo(state.continueString) || stream.skipToEnd();
    return "string";
  } else if (/[\d\.]/.test(ch)) {
    if (ch === ".")
      stream.match(/^[0-9]+([eE][\-+]?[0-9]+)?/);
    else if (ch === "0")
      stream.match(/^[xX][0-9a-fA-F]+/) || stream.match(/^0[0-7]+/);
    else
      stream.match(/^[0-9]*\.?[0-9]*([eE][\-+]?[0-9]+)?/);
    return "number";
  } else if (isDoubleOperatorChar.test(ch + stream.peek())) {
    stream.next();
    return "operator";
  } else if (isDoubleOperatorSym.hasOwnProperty(doubleOperator)) {
    stream.next();
    if (stream.peek() === " ")
      return isDoubleOperatorSym[doubleOperator.toLowerCase()];
  } else if (isSingleOperatorChar.test(ch)) {
    return "operator";
  }
  var word;
  if (stream.match(/[%&;\w]+/, false) != null) {
    word = ch + stream.match(/[%&;\w]+/, true);
    if (/&/.test(word)) return "variable";
  } else {
    word = ch;
  }
  if (state.nextword) {
    stream.match(/[\w]+/);
    if (stream.peek() === ".") stream.skipTo(" ");
    state.nextword = false;
    return "variableName.special";
  }
  word = word.toLowerCase();
  if (state.inDataStep) {
    if (word === "run;" || stream.match(/run\s;/)) {
      state.inDataStep = false;
      return "builtin";
    }
    if (word && stream.next() === ".") {
      if (/\w/.test(stream.peek())) return "variableName.special";
      else return "variable";
    }
    if (word && words6.hasOwnProperty(word) && (words6[word].state.indexOf("inDataStep") !== -1 || words6[word].state.indexOf("ALL") !== -1)) {
      if (stream.start < stream.pos)
        stream.backUp(stream.pos - stream.start);
      for (var i = 0; i < word.length; ++i) stream.next();
      return words6[word].style;
    }
  }
  if (state.inProc) {
    if (word === "run;" || word === "quit;") {
      state.inProc = false;
      return "builtin";
    }
    if (word && words6.hasOwnProperty(word) && (words6[word].state.indexOf("inProc") !== -1 || words6[word].state.indexOf("ALL") !== -1)) {
      stream.match(/[\w]+/);
      return words6[word].style;
    }
  }
  if (state.inMacro) {
    if (word === "%mend") {
      if (stream.peek() === ";") stream.next();
      state.inMacro = false;
      return "builtin";
    }
    if (word && words6.hasOwnProperty(word) && (words6[word].state.indexOf("inMacro") !== -1 || words6[word].state.indexOf("ALL") !== -1)) {
      stream.match(/[\w]+/);
      return words6[word].style;
    }
    return "atom";
  }
  if (word && words6.hasOwnProperty(word)) {
    stream.backUp(1);
    stream.match(/[\w]+/);
    if (word === "data" && /=/.test(stream.peek()) === false) {
      state.inDataStep = true;
      state.nextword = true;
      return "builtin";
    }
    if (word === "proc") {
      state.inProc = true;
      state.nextword = true;
      return "builtin";
    }
    if (word === "%macro") {
      state.inMacro = true;
      state.nextword = true;
      return "builtin";
    }
    if (/title[1-9]/.test(word)) return "def";
    if (word === "footnote") {
      stream.eat(/[1-9]/);
      return "def";
    }
    if (state.inDataStep === true && words6[word].state.indexOf("inDataStep") !== -1)
      return words6[word].style;
    if (state.inProc === true && words6[word].state.indexOf("inProc") !== -1)
      return words6[word].style;
    if (state.inMacro === true && words6[word].state.indexOf("inMacro") !== -1)
      return words6[word].style;
    if (words6[word].state.indexOf("ALL") !== -1)
      return words6[word].style;
    return null;
  }
  return null;
}
var sas = {
  name: "sas",
  startState: function() {
    return {
      inDataStep: false,
      inProc: false,
      inMacro: false,
      nextword: false,
      continueString: null,
      continueComment: false
    };
  },
  token: function(stream, state) {
    if (stream.eatSpace()) return null;
    return tokenize(stream, state);
  },
  languageData: {
    commentTokens: { block: { open: "/*", close: "*/" } }
  }
};

// node_modules/@codemirror/legacy-modes/mode/scheme.js
var BUILTIN = "builtin";
var COMMENT = "comment";
var STRING = "string";
var SYMBOL = "symbol";
var ATOM = "atom";
var NUMBER = "number";
var BRACKET = "bracket";
var INDENT_WORD_SKIP = 2;
function makeKeywords(str) {
  var obj = {}, words8 = str.split(" ");
  for (var i = 0; i < words8.length; ++i) obj[words8[i]] = true;
  return obj;
}
var keywords11 = makeKeywords("\u03BB case-lambda call/cc class cond-expand define-class define-values exit-handler field import inherit init-field interface let*-values let-values let/ec mixin opt-lambda override protect provide public rename require require-for-syntax syntax syntax-case syntax-error unit/sig unless when with-syntax and begin call-with-current-continuation call-with-input-file call-with-output-file case cond define define-syntax define-macro defmacro delay do dynamic-wind else for-each if lambda let let* let-syntax letrec letrec-syntax map or syntax-rules abs acos angle append apply asin assoc assq assv atan boolean? caar cadr call-with-input-file call-with-output-file call-with-values car cdddar cddddr cdr ceiling char->integer char-alphabetic? char-ci<=? char-ci<? char-ci=? char-ci>=? char-ci>? char-downcase char-lower-case? char-numeric? char-ready? char-upcase char-upper-case? char-whitespace? char<=? char<? char=? char>=? char>? char? close-input-port close-output-port complex? cons cos current-input-port current-output-port denominator display eof-object? eq? equal? eqv? eval even? exact->inexact exact? exp expt #f floor force gcd imag-part inexact->exact inexact? input-port? integer->char integer? interaction-environment lcm length list list->string list->vector list-ref list-tail list? load log magnitude make-polar make-rectangular make-string make-vector max member memq memv min modulo negative? newline not null-environment null? number->string number? numerator odd? open-input-file open-output-file output-port? pair? peek-char port? positive? procedure? quasiquote quote quotient rational? rationalize read read-char real-part real? remainder reverse round scheme-report-environment set! set-car! set-cdr! sin sqrt string string->list string->number string->symbol string-append string-ci<=? string-ci<? string-ci=? string-ci>=? string-ci>? string-copy string-fill! string-length string-ref string-set! string<=? string<? string=? string>=? string>? string? substring symbol->string symbol? #t tan transcript-off transcript-on truncate values vector vector->list vector-fill! vector-length vector-ref vector-set! with-input-from-file with-output-to-file write write-char zero?");
var indentKeys = makeKeywords("define let letrec let* lambda define-macro defmacro let-syntax letrec-syntax let-values let*-values define-syntax syntax-rules define-values when unless");
function stateStack(indent2, type2, prev) {
  this.indent = indent2;
  this.type = type2;
  this.prev = prev;
}
function pushStack(state, indent2, type2) {
  state.indentStack = new stateStack(indent2, type2, state.indentStack);
}
function popStack(state) {
  state.indentStack = state.indentStack.prev;
}
var binaryMatcher = new RegExp(/^(?:[-+]i|[-+][01]+#*(?:\/[01]+#*)?i|[-+]?[01]+#*(?:\/[01]+#*)?@[-+]?[01]+#*(?:\/[01]+#*)?|[-+]?[01]+#*(?:\/[01]+#*)?[-+](?:[01]+#*(?:\/[01]+#*)?)?i|[-+]?[01]+#*(?:\/[01]+#*)?)(?=[()\s;"]|$)/i);
var octalMatcher = new RegExp(/^(?:[-+]i|[-+][0-7]+#*(?:\/[0-7]+#*)?i|[-+]?[0-7]+#*(?:\/[0-7]+#*)?@[-+]?[0-7]+#*(?:\/[0-7]+#*)?|[-+]?[0-7]+#*(?:\/[0-7]+#*)?[-+](?:[0-7]+#*(?:\/[0-7]+#*)?)?i|[-+]?[0-7]+#*(?:\/[0-7]+#*)?)(?=[()\s;"]|$)/i);
var hexMatcher = new RegExp(/^(?:[-+]i|[-+][\da-f]+#*(?:\/[\da-f]+#*)?i|[-+]?[\da-f]+#*(?:\/[\da-f]+#*)?@[-+]?[\da-f]+#*(?:\/[\da-f]+#*)?|[-+]?[\da-f]+#*(?:\/[\da-f]+#*)?[-+](?:[\da-f]+#*(?:\/[\da-f]+#*)?)?i|[-+]?[\da-f]+#*(?:\/[\da-f]+#*)?)(?=[()\s;"]|$)/i);
var decimalMatcher = new RegExp(/^(?:[-+]i|[-+](?:(?:(?:\d+#+\.?#*|\d+\.\d*#*|\.\d+#*|\d+)(?:[esfdl][-+]?\d+)?)|\d+#*\/\d+#*)i|[-+]?(?:(?:(?:\d+#+\.?#*|\d+\.\d*#*|\.\d+#*|\d+)(?:[esfdl][-+]?\d+)?)|\d+#*\/\d+#*)@[-+]?(?:(?:(?:\d+#+\.?#*|\d+\.\d*#*|\.\d+#*|\d+)(?:[esfdl][-+]?\d+)?)|\d+#*\/\d+#*)|[-+]?(?:(?:(?:\d+#+\.?#*|\d+\.\d*#*|\.\d+#*|\d+)(?:[esfdl][-+]?\d+)?)|\d+#*\/\d+#*)[-+](?:(?:(?:\d+#+\.?#*|\d+\.\d*#*|\.\d+#*|\d+)(?:[esfdl][-+]?\d+)?)|\d+#*\/\d+#*)?i|(?:(?:(?:\d+#+\.?#*|\d+\.\d*#*|\.\d+#*|\d+)(?:[esfdl][-+]?\d+)?)|\d+#*\/\d+#*))(?=[()\s;"]|$)/i);
function isBinaryNumber(stream) {
  return stream.match(binaryMatcher);
}
function isOctalNumber(stream) {
  return stream.match(octalMatcher);
}
function isDecimalNumber(stream, backup) {
  if (backup === true) {
    stream.backUp(1);
  }
  return stream.match(decimalMatcher);
}
function isHexNumber(stream) {
  return stream.match(hexMatcher);
}
function processEscapedSequence(stream, options) {
  var next, escaped = false;
  while ((next = stream.next()) != null) {
    if (next == options.token && !escaped) {
      options.state.mode = false;
      break;
    }
    escaped = !escaped && next == "\\";
  }
}
var scheme = {
  name: "scheme",
  startState: function() {
    return {
      indentStack: null,
      indentation: 0,
      mode: false,
      sExprComment: false,
      sExprQuote: false
    };
  },
  token: function(stream, state) {
    if (state.indentStack == null && stream.sol()) {
      state.indentation = stream.indentation();
    }
    if (stream.eatSpace()) {
      return null;
    }
    var returnType = null;
    switch (state.mode) {
      case "string":
        processEscapedSequence(stream, {
          token: '"',
          state
        });
        returnType = STRING;
        break;
      case "symbol":
        processEscapedSequence(stream, {
          token: "|",
          state
        });
        returnType = SYMBOL;
        break;
      case "comment":
        var next, maybeEnd = false;
        while ((next = stream.next()) != null) {
          if (next == "#" && maybeEnd) {
            state.mode = false;
            break;
          }
          maybeEnd = next == "|";
        }
        returnType = COMMENT;
        break;
      case "s-expr-comment":
        state.mode = false;
        if (stream.peek() == "(" || stream.peek() == "[") {
          state.sExprComment = 0;
        } else {
          stream.eatWhile(/[^\s\(\)\[\]]/);
          returnType = COMMENT;
          break;
        }
      default:
        var ch = stream.next();
        if (ch == '"') {
          state.mode = "string";
          returnType = STRING;
        } else if (ch == "'") {
          if (stream.peek() == "(" || stream.peek() == "[") {
            if (typeof state.sExprQuote != "number") {
              state.sExprQuote = 0;
            }
            returnType = ATOM;
          } else {
            stream.eatWhile(/[\w_\-!$%&*+\.\/:<=>?@\^~]/);
            returnType = ATOM;
          }
        } else if (ch == "|") {
          state.mode = "symbol";
          returnType = SYMBOL;
        } else if (ch == "#") {
          if (stream.eat("|")) {
            state.mode = "comment";
            returnType = COMMENT;
          } else if (stream.eat(/[tf]/i)) {
            returnType = ATOM;
          } else if (stream.eat(";")) {
            state.mode = "s-expr-comment";
            returnType = COMMENT;
          } else {
            var numTest = null, hasExactness = false, hasRadix = true;
            if (stream.eat(/[ei]/i)) {
              hasExactness = true;
            } else {
              stream.backUp(1);
            }
            if (stream.match(/^#b/i)) {
              numTest = isBinaryNumber;
            } else if (stream.match(/^#o/i)) {
              numTest = isOctalNumber;
            } else if (stream.match(/^#x/i)) {
              numTest = isHexNumber;
            } else if (stream.match(/^#d/i)) {
              numTest = isDecimalNumber;
            } else if (stream.match(/^[-+0-9.]/, false)) {
              hasRadix = false;
              numTest = isDecimalNumber;
            } else if (!hasExactness) {
              stream.eat("#");
            }
            if (numTest != null) {
              if (hasRadix && !hasExactness) {
                stream.match(/^#[ei]/i);
              }
              if (numTest(stream))
                returnType = NUMBER;
            }
          }
        } else if (/^[-+0-9.]/.test(ch) && isDecimalNumber(stream, true)) {
          returnType = NUMBER;
        } else if (ch == ";") {
          stream.skipToEnd();
          returnType = COMMENT;
        } else if (ch == "(" || ch == "[") {
          var keyWord = "";
          var indentTemp = stream.column(), letter;
          while ((letter = stream.eat(/[^\s\(\[\;\)\]]/)) != null) {
            keyWord += letter;
          }
          if (keyWord.length > 0 && indentKeys.propertyIsEnumerable(keyWord)) {
            pushStack(state, indentTemp + INDENT_WORD_SKIP, ch);
          } else {
            stream.eatSpace();
            if (stream.eol() || stream.peek() == ";") {
              pushStack(state, indentTemp + 1, ch);
            } else {
              pushStack(state, indentTemp + stream.current().length, ch);
            }
          }
          stream.backUp(stream.current().length - 1);
          if (typeof state.sExprComment == "number") state.sExprComment++;
          if (typeof state.sExprQuote == "number") state.sExprQuote++;
          returnType = BRACKET;
        } else if (ch == ")" || ch == "]") {
          returnType = BRACKET;
          if (state.indentStack != null && state.indentStack.type == (ch == ")" ? "(" : "[")) {
            popStack(state);
            if (typeof state.sExprComment == "number") {
              if (--state.sExprComment == 0) {
                returnType = COMMENT;
                state.sExprComment = false;
              }
            }
            if (typeof state.sExprQuote == "number") {
              if (--state.sExprQuote == 0) {
                returnType = ATOM;
                state.sExprQuote = false;
              }
            }
          }
        } else {
          stream.eatWhile(/[\w_\-!$%&*+\.\/:<=>?@\^~]/);
          if (keywords11 && keywords11.propertyIsEnumerable(stream.current())) {
            returnType = BUILTIN;
          } else returnType = "variable";
        }
    }
    return typeof state.sExprComment == "number" ? COMMENT : typeof state.sExprQuote == "number" ? ATOM : returnType;
  },
  indent: function(state) {
    if (state.indentStack == null) return state.indentation;
    return state.indentStack.indent;
  },
  languageData: {
    closeBrackets: { brackets: ["(", "[", "{", '"'] },
    commentTokens: { line: ";;" }
  }
};

// node_modules/@codemirror/legacy-modes/mode/shell.js
var words7 = {};
function define2(style, dict) {
  for (var i = 0; i < dict.length; i++) {
    words7[dict[i]] = style;
  }
}
var commonAtoms2 = ["true", "false"];
var commonKeywords3 = [
  "if",
  "then",
  "do",
  "else",
  "elif",
  "while",
  "until",
  "for",
  "in",
  "esac",
  "fi",
  "fin",
  "fil",
  "done",
  "exit",
  "set",
  "unset",
  "export",
  "function"
];
var commonCommands = [
  "ab",
  "awk",
  "bash",
  "beep",
  "cat",
  "cc",
  "cd",
  "chown",
  "chmod",
  "chroot",
  "clear",
  "cp",
  "curl",
  "cut",
  "diff",
  "echo",
  "find",
  "gawk",
  "gcc",
  "get",
  "git",
  "grep",
  "hg",
  "kill",
  "killall",
  "ln",
  "ls",
  "make",
  "mkdir",
  "openssl",
  "mv",
  "nc",
  "nl",
  "node",
  "npm",
  "ping",
  "ps",
  "restart",
  "rm",
  "rmdir",
  "sed",
  "service",
  "sh",
  "shopt",
  "shred",
  "source",
  "sort",
  "sleep",
  "ssh",
  "start",
  "stop",
  "su",
  "sudo",
  "svn",
  "tee",
  "telnet",
  "top",
  "touch",
  "vi",
  "vim",
  "wall",
  "wc",
  "wget",
  "who",
  "write",
  "yes",
  "zsh"
];
define2("atom", commonAtoms2);
define2("keyword", commonKeywords3);
define2("builtin", commonCommands);
function tokenBase10(stream, state) {
  if (stream.eatSpace()) return null;
  var sol = stream.sol();
  var ch = stream.next();
  if (ch === "\\") {
    stream.next();
    return null;
  }
  if (ch === "'" || ch === '"' || ch === "`") {
    state.tokens.unshift(tokenString6(ch, ch === "`" ? "quote" : "string"));
    return tokenize2(stream, state);
  }
  if (ch === "#") {
    if (sol && stream.eat("!")) {
      stream.skipToEnd();
      return "meta";
    }
    stream.skipToEnd();
    return "comment";
  }
  if (ch === "$") {
    state.tokens.unshift(tokenDollar);
    return tokenize2(stream, state);
  }
  if (ch === "+" || ch === "=") {
    return "operator";
  }
  if (ch === "-") {
    stream.eat("-");
    stream.eatWhile(/\w/);
    return "attribute";
  }
  if (ch == "<") {
    if (stream.match("<<")) return "operator";
    var heredoc = stream.match(/^<-?\s*(?:['"]([^'"]*)['"]|([^'"\s]*))/);
    if (heredoc) {
      state.tokens.unshift(tokenHeredoc(heredoc[1] || heredoc[2]));
      return "string.special";
    }
  }
  if (/\d/.test(ch)) {
    stream.eatWhile(/\d/);
    if (stream.eol() || !/\w/.test(stream.peek())) {
      return "number";
    }
  }
  stream.eatWhile(/[\w-]/);
  var cur = stream.current();
  if (stream.peek() === "=" && /\w+/.test(cur)) return "def";
  return words7.hasOwnProperty(cur) ? words7[cur] : null;
}
function tokenString6(quote, style) {
  var close = quote == "(" ? ")" : quote == "{" ? "}" : quote;
  return function(stream, state) {
    var next, escaped = false;
    while ((next = stream.next()) != null) {
      if (next === close && !escaped) {
        state.tokens.shift();
        break;
      } else if (next === "$" && !escaped && quote !== "'" && stream.peek() != close) {
        escaped = true;
        stream.backUp(1);
        state.tokens.unshift(tokenDollar);
        break;
      } else if (!escaped && quote !== close && next === quote) {
        state.tokens.unshift(tokenString6(quote, style));
        return tokenize2(stream, state);
      } else if (!escaped && /['"]/.test(next) && !/['"]/.test(quote)) {
        state.tokens.unshift(tokenStringStart(next, "string"));
        stream.backUp(1);
        break;
      }
      escaped = !escaped && next === "\\";
    }
    return style;
  };
}
function tokenStringStart(quote, style) {
  return function(stream, state) {
    state.tokens[0] = tokenString6(quote, style);
    stream.next();
    return tokenize2(stream, state);
  };
}
var tokenDollar = function(stream, state) {
  if (state.tokens.length > 1) stream.eat("$");
  var ch = stream.next();
  if (/['"({]/.test(ch)) {
    state.tokens[0] = tokenString6(ch, ch == "(" ? "quote" : ch == "{" ? "def" : "string");
    return tokenize2(stream, state);
  }
  if (!/\d/.test(ch)) stream.eatWhile(/\w/);
  state.tokens.shift();
  return "def";
};
function tokenHeredoc(delim) {
  return function(stream, state) {
    if (stream.sol() && stream.string == delim) state.tokens.shift();
    stream.skipToEnd();
    return "string.special";
  };
}
function tokenize2(stream, state) {
  return (state.tokens[0] || tokenBase10)(stream, state);
}
var shell = {
  name: "shell",
  startState: function() {
    return { tokens: [] };
  },
  token: function(stream, state) {
    return tokenize2(stream, state);
  },
  languageData: {
    autocomplete: commonAtoms2.concat(commonKeywords3, commonCommands),
    closeBrackets: { brackets: ["(", "[", "{", "'", '"', "`"] },
    commentTokens: { line: "#" }
  }
};

// node_modules/@codemirror/legacy-modes/mode/sql.js
function sql(parserConfig) {
  var client = parserConfig.client || {}, atoms6 = parserConfig.atoms || { "false": true, "true": true, "null": true }, builtin = parserConfig.builtin || set(defaultBuiltin), keywords14 = parserConfig.keywords || set(sqlKeywords), operatorChars = parserConfig.operatorChars || /^[*+\-%<>!=&|~^\/]/, support = parserConfig.support || {}, hooks = parserConfig.hooks || {}, dateSQL = parserConfig.dateSQL || { "date": true, "time": true, "timestamp": true }, backslashStringEscapes = parserConfig.backslashStringEscapes !== false, brackets = parserConfig.brackets || /^[\{}\(\)\[\]]/, punctuation2 = parserConfig.punctuation || /^[;.,:]/;
  function tokenBase13(stream, state) {
    var ch = stream.next();
    if (hooks[ch]) {
      var result = hooks[ch](stream, state);
      if (result !== false) return result;
    }
    if (support.hexNumber && (ch == "0" && stream.match(/^[xX][0-9a-fA-F]+/) || (ch == "x" || ch == "X") && stream.match(/^'[0-9a-fA-F]*'/))) {
      return "number";
    } else if (support.binaryNumber && ((ch == "b" || ch == "B") && stream.match(/^'[01]+'/) || ch == "0" && stream.match(/^b[01]*/))) {
      return "number";
    } else if (ch.charCodeAt(0) > 47 && ch.charCodeAt(0) < 58) {
      stream.match(/^[0-9]*(\.[0-9]+)?([eE][-+]?[0-9]+)?/);
      support.decimallessFloat && stream.match(/^\.(?!\.)/);
      return "number";
    } else if (ch == "?" && (stream.eatSpace() || stream.eol() || stream.eat(";"))) {
      return "macroName";
    } else if (ch == "'" || ch == '"' && support.doubleQuote) {
      state.tokenize = tokenLiteral(ch);
      return state.tokenize(stream, state);
    } else if ((support.nCharCast && (ch == "n" || ch == "N") || support.charsetCast && ch == "_" && stream.match(/[a-z][a-z0-9]*/i)) && (stream.peek() == "'" || stream.peek() == '"')) {
      return "keyword";
    } else if (support.escapeConstant && (ch == "e" || ch == "E") && (stream.peek() == "'" || stream.peek() == '"' && support.doubleQuote)) {
      state.tokenize = function(stream2, state2) {
        return (state2.tokenize = tokenLiteral(stream2.next(), true))(stream2, state2);
      };
      return "keyword";
    } else if (support.commentSlashSlash && ch == "/" && stream.eat("/")) {
      stream.skipToEnd();
      return "comment";
    } else if (support.commentHash && ch == "#" || ch == "-" && stream.eat("-") && (!support.commentSpaceRequired || stream.eat(" "))) {
      stream.skipToEnd();
      return "comment";
    } else if (ch == "/" && stream.eat("*")) {
      state.tokenize = tokenComment7(1);
      return state.tokenize(stream, state);
    } else if (ch == ".") {
      if (support.zerolessFloat && stream.match(/^(?:\d+(?:e[+-]?\d+)?)/i))
        return "number";
      if (stream.match(/^\.+/))
        return null;
      if (support.ODBCdotTable && stream.match(/^[\w\d_$#]+/))
        return "type";
    } else if (operatorChars.test(ch)) {
      stream.eatWhile(operatorChars);
      return "operator";
    } else if (brackets.test(ch)) {
      return "bracket";
    } else if (punctuation2.test(ch)) {
      stream.eatWhile(punctuation2);
      return "punctuation";
    } else if (ch == "{" && (stream.match(/^( )*(d|D|t|T|ts|TS)( )*'[^']*'( )*}/) || stream.match(/^( )*(d|D|t|T|ts|TS)( )*"[^"]*"( )*}/))) {
      return "number";
    } else {
      stream.eatWhile(/^[_\w\d]/);
      var word = stream.current().toLowerCase();
      if (dateSQL.hasOwnProperty(word) && (stream.match(/^( )+'[^']*'/) || stream.match(/^( )+"[^"]*"/)))
        return "number";
      if (atoms6.hasOwnProperty(word)) return "atom";
      if (builtin.hasOwnProperty(word)) return "type";
      if (keywords14.hasOwnProperty(word)) return "keyword";
      if (client.hasOwnProperty(word)) return "builtin";
      return null;
    }
  }
  function tokenLiteral(quote, backslashEscapes) {
    return function(stream, state) {
      var escaped = false, ch;
      while ((ch = stream.next()) != null) {
        if (ch == quote && !escaped) {
          state.tokenize = tokenBase13;
          break;
        }
        escaped = (backslashStringEscapes || backslashEscapes) && !escaped && ch == "\\";
      }
      return "string";
    };
  }
  function tokenComment7(depth) {
    return function(stream, state) {
      var m = stream.match(/^.*?(\/\*|\*\/)/);
      if (!m) stream.skipToEnd();
      else if (m[1] == "/*") state.tokenize = tokenComment7(depth + 1);
      else if (depth > 1) state.tokenize = tokenComment7(depth - 1);
      else state.tokenize = tokenBase13;
      return "comment";
    };
  }
  function pushContext4(stream, state, type2) {
    state.context = {
      prev: state.context,
      indent: stream.indentation(),
      col: stream.column(),
      type: type2
    };
  }
  function popContext4(state) {
    state.indent = state.context.indent;
    state.context = state.context.prev;
  }
  return {
    name: "sql",
    startState: function() {
      return { tokenize: tokenBase13, context: null };
    },
    token: function(stream, state) {
      if (stream.sol()) {
        if (state.context && state.context.align == null)
          state.context.align = false;
      }
      if (state.tokenize == tokenBase13 && stream.eatSpace()) return null;
      var style = state.tokenize(stream, state);
      if (style == "comment") return style;
      if (state.context && state.context.align == null)
        state.context.align = true;
      var tok = stream.current();
      if (tok == "(")
        pushContext4(stream, state, ")");
      else if (tok == "[")
        pushContext4(stream, state, "]");
      else if (state.context && state.context.type == tok)
        popContext4(state);
      return style;
    },
    indent: function(state, textAfter, iCx) {
      var cx = state.context;
      if (!cx) return null;
      var closing3 = textAfter.charAt(0) == cx.type;
      if (cx.align) return cx.col + (closing3 ? 0 : 1);
      else return cx.indent + (closing3 ? 0 : iCx.unit);
    },
    languageData: {
      commentTokens: {
        line: support.commentSlashSlash ? "//" : support.commentHash ? "#" : "--",
        block: { open: "/*", close: "*/" }
      },
      closeBrackets: { brackets: ["(", "[", "{", "'", '"', "`"] }
    }
  };
}
function hookIdentifier(stream) {
  var ch;
  while ((ch = stream.next()) != null) {
    if (ch == "`" && !stream.eat("`")) return "string.special";
  }
  stream.backUp(stream.current().length - 1);
  return stream.eatWhile(/\w/) ? "string.special" : null;
}
function hookIdentifierDoublequote(stream) {
  var ch;
  while ((ch = stream.next()) != null) {
    if (ch == '"' && !stream.eat('"')) return "string.special";
  }
  stream.backUp(stream.current().length - 1);
  return stream.eatWhile(/\w/) ? "string.special" : null;
}
function hookVar(stream) {
  if (stream.eat("@")) {
    stream.match("session.");
    stream.match("local.");
    stream.match("global.");
  }
  if (stream.eat("'")) {
    stream.match(/^.*'/);
    return "string.special";
  } else if (stream.eat('"')) {
    stream.match(/^.*"/);
    return "string.special";
  } else if (stream.eat("`")) {
    stream.match(/^.*`/);
    return "string.special";
  } else if (stream.match(/^[0-9a-zA-Z$\.\_]+/)) {
    return "string.special";
  }
  return null;
}
function hookClient(stream) {
  if (stream.eat("N")) {
    return "atom";
  }
  return stream.match(/^[a-zA-Z.#!?]/) ? "string.special" : null;
}
var sqlKeywords = "alter and as asc between by count create delete desc distinct drop from group having in insert into is join like not on or order select set table union update values where limit ";
function set(str) {
  var obj = {}, words8 = str.split(" ");
  for (var i = 0; i < words8.length; ++i) obj[words8[i]] = true;
  return obj;
}
var defaultBuiltin = "bool boolean bit blob enum long longblob longtext medium mediumblob mediumint mediumtext time timestamp tinyblob tinyint tinytext text bigint int int1 int2 int3 int4 int8 integer float float4 float8 double char varbinary varchar varcharacter precision real date datetime year unsigned signed decimal numeric";
var standardSQL = sql({
  keywords: set(sqlKeywords + "begin"),
  builtin: set(defaultBuiltin),
  atoms: set("false true null unknown"),
  dateSQL: set("date time timestamp"),
  support: set("ODBCdotTable doubleQuote binaryNumber hexNumber")
});
var msSQL = sql({
  client: set("$partition binary_checksum checksum connectionproperty context_info current_request_id error_line error_message error_number error_procedure error_severity error_state formatmessage get_filestream_transaction_context getansinull host_id host_name isnull isnumeric min_active_rowversion newid newsequentialid rowcount_big xact_state object_id"),
  keywords: set(sqlKeywords + "begin trigger proc view index for add constraint key primary foreign collate clustered nonclustered declare exec go if use index holdlock nolock nowait paglock readcommitted readcommittedlock readpast readuncommitted repeatableread rowlock serializable snapshot tablock tablockx updlock with"),
  builtin: set("bigint numeric bit smallint decimal smallmoney int tinyint money float real char varchar text nchar nvarchar ntext binary varbinary image cursor timestamp hierarchyid uniqueidentifier sql_variant xml table "),
  atoms: set("is not null like and or in left right between inner outer join all any some cross unpivot pivot exists"),
  operatorChars: /^[*+\-%<>!=^\&|\/]/,
  brackets: /^[\{}\(\)]/,
  punctuation: /^[;.,:/]/,
  backslashStringEscapes: false,
  dateSQL: set("date datetimeoffset datetime2 smalldatetime datetime time"),
  hooks: {
    "@": hookVar
  }
});
var mySQL = sql({
  client: set("charset clear connect edit ego exit go help nopager notee nowarning pager print prompt quit rehash source status system tee"),
  keywords: set(sqlKeywords + "accessible action add after algorithm all analyze asensitive at authors auto_increment autocommit avg avg_row_length before binary binlog both btree cache call cascade cascaded case catalog_name chain change changed character check checkpoint checksum class_origin client_statistics close coalesce code collate collation collations column columns comment commit committed completion concurrent condition connection consistent constraint contains continue contributors convert cross current current_date current_time current_timestamp current_user cursor data database databases day_hour day_microsecond day_minute day_second deallocate dec declare default delay_key_write delayed delimiter des_key_file describe deterministic dev_pop dev_samp deviance diagnostics directory disable discard distinctrow div dual dumpfile each elseif enable enclosed end ends engine engines enum errors escape escaped even event events every execute exists exit explain extended fast fetch field fields first flush for force foreign found_rows full fulltext function general get global grant grants group group_concat handler hash help high_priority hosts hour_microsecond hour_minute hour_second if ignore ignore_server_ids import index index_statistics infile inner innodb inout insensitive insert_method install interval invoker isolation iterate key keys kill language last leading leave left level limit linear lines list load local localtime localtimestamp lock logs low_priority master master_heartbeat_period master_ssl_verify_server_cert masters match max max_rows maxvalue message_text middleint migrate min min_rows minute_microsecond minute_second mod mode modifies modify mutex mysql_errno natural next no no_write_to_binlog offline offset one online open optimize option optionally out outer outfile pack_keys parser partition partitions password phase plugin plugins prepare preserve prev primary privileges procedure processlist profile profiles purge query quick range read read_write reads real rebuild recover references regexp relaylog release remove rename reorganize repair repeatable replace require resignal restrict resume return returns revoke right rlike rollback rollup row row_format rtree savepoint schedule schema schema_name schemas second_microsecond security sensitive separator serializable server session share show signal slave slow smallint snapshot soname spatial specific sql sql_big_result sql_buffer_result sql_cache sql_calc_found_rows sql_no_cache sql_small_result sqlexception sqlstate sqlwarning ssl start starting starts status std stddev stddev_pop stddev_samp storage straight_join subclass_origin sum suspend table_name table_statistics tables tablespace temporary terminated to trailing transaction trigger triggers truncate uncommitted undo uninstall unique unlock upgrade usage use use_frm user user_resources user_statistics using utc_date utc_time utc_timestamp value variables varying view views warnings when while with work write xa xor year_month zerofill begin do then else loop repeat"),
  builtin: set("bool boolean bit blob decimal double float long longblob longtext medium mediumblob mediumint mediumtext time timestamp tinyblob tinyint tinytext text bigint int int1 int2 int3 int4 int8 integer float float4 float8 double char varbinary varchar varcharacter precision date datetime year unsigned signed numeric"),
  atoms: set("false true null unknown"),
  operatorChars: /^[*+\-%<>!=&|^]/,
  dateSQL: set("date time timestamp"),
  support: set("ODBCdotTable decimallessFloat zerolessFloat binaryNumber hexNumber doubleQuote nCharCast charsetCast commentHash commentSpaceRequired"),
  hooks: {
    "@": hookVar,
    "`": hookIdentifier,
    "\\": hookClient
  }
});
var mariaDB = sql({
  client: set("charset clear connect edit ego exit go help nopager notee nowarning pager print prompt quit rehash source status system tee"),
  keywords: set(sqlKeywords + "accessible action add after algorithm all always analyze asensitive at authors auto_increment autocommit avg avg_row_length before binary binlog both btree cache call cascade cascaded case catalog_name chain change changed character check checkpoint checksum class_origin client_statistics close coalesce code collate collation collations column columns comment commit committed completion concurrent condition connection consistent constraint contains continue contributors convert cross current current_date current_time current_timestamp current_user cursor data database databases day_hour day_microsecond day_minute day_second deallocate dec declare default delay_key_write delayed delimiter des_key_file describe deterministic dev_pop dev_samp deviance diagnostics directory disable discard distinctrow div dual dumpfile each elseif enable enclosed end ends engine engines enum errors escape escaped even event events every execute exists exit explain extended fast fetch field fields first flush for force foreign found_rows full fulltext function general generated get global grant grants group group_concat handler hard hash help high_priority hosts hour_microsecond hour_minute hour_second if ignore ignore_server_ids import index index_statistics infile inner innodb inout insensitive insert_method install interval invoker isolation iterate key keys kill language last leading leave left level limit linear lines list load local localtime localtimestamp lock logs low_priority master master_heartbeat_period master_ssl_verify_server_cert masters match max max_rows maxvalue message_text middleint migrate min min_rows minute_microsecond minute_second mod mode modifies modify mutex mysql_errno natural next no no_write_to_binlog offline offset one online open optimize option optionally out outer outfile pack_keys parser partition partitions password persistent phase plugin plugins prepare preserve prev primary privileges procedure processlist profile profiles purge query quick range read read_write reads real rebuild recover references regexp relaylog release remove rename reorganize repair repeatable replace require resignal restrict resume return returns revoke right rlike rollback rollup row row_format rtree savepoint schedule schema schema_name schemas second_microsecond security sensitive separator serializable server session share show shutdown signal slave slow smallint snapshot soft soname spatial specific sql sql_big_result sql_buffer_result sql_cache sql_calc_found_rows sql_no_cache sql_small_result sqlexception sqlstate sqlwarning ssl start starting starts status std stddev stddev_pop stddev_samp storage straight_join subclass_origin sum suspend table_name table_statistics tables tablespace temporary terminated to trailing transaction trigger triggers truncate uncommitted undo uninstall unique unlock upgrade usage use use_frm user user_resources user_statistics using utc_date utc_time utc_timestamp value variables varying view views virtual warnings when while with work write xa xor year_month zerofill begin do then else loop repeat"),
  builtin: set("bool boolean bit blob decimal double float long longblob longtext medium mediumblob mediumint mediumtext time timestamp tinyblob tinyint tinytext text bigint int int1 int2 int3 int4 int8 integer float float4 float8 double char varbinary varchar varcharacter precision date datetime year unsigned signed numeric"),
  atoms: set("false true null unknown"),
  operatorChars: /^[*+\-%<>!=&|^]/,
  dateSQL: set("date time timestamp"),
  support: set("ODBCdotTable decimallessFloat zerolessFloat binaryNumber hexNumber doubleQuote nCharCast charsetCast commentHash commentSpaceRequired"),
  hooks: {
    "@": hookVar,
    "`": hookIdentifier,
    "\\": hookClient
  }
});
var sqlite = sql({
  // commands of the official SQLite client, ref: https://www.sqlite.org/cli.html#dotcmd
  client: set("auth backup bail binary changes check clone databases dbinfo dump echo eqp exit explain fullschema headers help import imposter indexes iotrace limit lint load log mode nullvalue once open output print prompt quit read restore save scanstats schema separator session shell show stats system tables testcase timeout timer trace vfsinfo vfslist vfsname width"),
  // ref: http://sqlite.org/lang_keywords.html
  keywords: set(sqlKeywords + "abort action add after all analyze attach autoincrement before begin cascade case cast check collate column commit conflict constraint cross current_date current_time current_timestamp database default deferrable deferred detach each else end escape except exclusive exists explain fail for foreign full glob if ignore immediate index indexed initially inner instead intersect isnull key left limit match natural no notnull null of offset outer plan pragma primary query raise recursive references regexp reindex release rename replace restrict right rollback row savepoint temp temporary then to transaction trigger unique using vacuum view virtual when with without"),
  // SQLite is weakly typed, ref: http://sqlite.org/datatype3.html. This is just a list of some common types.
  builtin: set("bool boolean bit blob decimal double float long longblob longtext medium mediumblob mediumint mediumtext time timestamp tinyblob tinyint tinytext text clob bigint int int2 int8 integer float double char varchar date datetime year unsigned signed numeric real"),
  // ref: http://sqlite.org/syntax/literal-value.html
  atoms: set("null current_date current_time current_timestamp"),
  // ref: http://sqlite.org/lang_expr.html#binaryops
  operatorChars: /^[*+\-%<>!=&|/~]/,
  // SQLite is weakly typed, ref: http://sqlite.org/datatype3.html. This is just a list of some common types.
  dateSQL: set("date time timestamp datetime"),
  support: set("decimallessFloat zerolessFloat"),
  identifierQuote: '"',
  //ref: http://sqlite.org/lang_keywords.html
  hooks: {
    // bind-parameters ref:http://sqlite.org/lang_expr.html#varparam
    "@": hookVar,
    ":": hookVar,
    "?": hookVar,
    "$": hookVar,
    // The preferred way to escape Identifiers is using double quotes, ref: http://sqlite.org/lang_keywords.html
    '"': hookIdentifierDoublequote,
    // there is also support for backticks, ref: http://sqlite.org/lang_keywords.html
    "`": hookIdentifier
  }
});
var cassandra = sql({
  client: {},
  keywords: set("add all allow alter and any apply as asc authorize batch begin by clustering columnfamily compact consistency count create custom delete desc distinct drop each_quorum exists filtering from grant if in index insert into key keyspace keyspaces level limit local_one local_quorum modify nan norecursive nosuperuser not of on one order password permission permissions primary quorum rename revoke schema select set storage superuser table three to token truncate ttl two type unlogged update use user users using values where with writetime"),
  builtin: set("ascii bigint blob boolean counter decimal double float frozen inet int list map static text timestamp timeuuid tuple uuid varchar varint"),
  atoms: set("false true infinity NaN"),
  operatorChars: /^[<>=]/,
  dateSQL: {},
  support: set("commentSlashSlash decimallessFloat"),
  hooks: {}
});
var plSQL = sql({
  client: set("appinfo arraysize autocommit autoprint autorecovery autotrace blockterminator break btitle cmdsep colsep compatibility compute concat copycommit copytypecheck define describe echo editfile embedded escape exec execute feedback flagger flush heading headsep instance linesize lno loboffset logsource long longchunksize markup native newpage numformat numwidth pagesize pause pno recsep recsepchar release repfooter repheader serveroutput shiftinout show showmode size spool sqlblanklines sqlcase sqlcode sqlcontinue sqlnumber sqlpluscompatibility sqlprefix sqlprompt sqlterminator suffix tab term termout time timing trimout trimspool ttitle underline verify version wrap"),
  keywords: set("abort accept access add all alter and any array arraylen as asc assert assign at attributes audit authorization avg base_table begin between binary_integer body boolean by case cast char char_base check close cluster clusters colauth column comment commit compress connect connected constant constraint crash create current currval cursor data_base database date dba deallocate debugoff debugon decimal declare default definition delay delete desc digits dispose distinct do drop else elseif elsif enable end entry escape exception exception_init exchange exclusive exists exit external fast fetch file for force form from function generic goto grant group having identified if immediate in increment index indexes indicator initial initrans insert interface intersect into is key level library like limited local lock log logging long loop master maxextents maxtrans member minextents minus mislabel mode modify multiset new next no noaudit nocompress nologging noparallel not nowait number_base object of off offline on online only open option or order out package parallel partition pctfree pctincrease pctused pls_integer positive positiven pragma primary prior private privileges procedure public raise range raw read rebuild record ref references refresh release rename replace resource restrict return returning returns reverse revoke rollback row rowid rowlabel rownum rows run savepoint schema segment select separate session set share snapshot some space split sql start statement storage subtype successful synonym tabauth table tables tablespace task terminate then to trigger truncate type union unique unlimited unrecoverable unusable update use using validate value values variable view views when whenever where while with work"),
  builtin: set("abs acos add_months ascii asin atan atan2 average bfile bfilename bigserial bit blob ceil character chartorowid chr clob concat convert cos cosh count dec decode deref dual dump dup_val_on_index empty error exp false float floor found glb greatest hextoraw initcap instr instrb int integer isopen last_day least length lengthb ln lower lpad ltrim lub make_ref max min mlslabel mod months_between natural naturaln nchar nclob new_time next_day nextval nls_charset_decl_len nls_charset_id nls_charset_name nls_initcap nls_lower nls_sort nls_upper nlssort no_data_found notfound null number numeric nvarchar2 nvl others power rawtohex real reftohex round rowcount rowidtochar rowtype rpad rtrim serial sign signtype sin sinh smallint soundex sqlcode sqlerrm sqrt stddev string substr substrb sum sysdate tan tanh to_char text to_date to_label to_multi_byte to_number to_single_byte translate true trunc uid unlogged upper user userenv varchar varchar2 variance varying vsize xml"),
  operatorChars: /^[*\/+\-%<>!=~]/,
  dateSQL: set("date time timestamp"),
  support: set("doubleQuote nCharCast zerolessFloat binaryNumber hexNumber")
});
var hive = sql({
  keywords: set("select alter $elem$ $key$ $value$ add after all analyze and archive as asc before between binary both bucket buckets by cascade case cast change cluster clustered clusterstatus collection column columns comment compute concatenate continue create cross cursor data database databases dbproperties deferred delete delimited desc describe directory disable distinct distribute drop else enable end escaped exclusive exists explain export extended external fetch fields fileformat first format formatted from full function functions grant group having hold_ddltime idxproperties if import in index indexes inpath inputdriver inputformat insert intersect into is items join keys lateral left like limit lines load local location lock locks mapjoin materialized minus msck no_drop nocompress not of offline on option or order out outer outputdriver outputformat overwrite partition partitioned partitions percent plus preserve procedure purge range rcfile read readonly reads rebuild recordreader recordwriter recover reduce regexp rename repair replace restrict revoke right rlike row schema schemas semi sequencefile serde serdeproperties set shared show show_database sort sorted ssl statistics stored streamtable table tables tablesample tblproperties temporary terminated textfile then tmp to touch transform trigger unarchive undo union uniquejoin unlock update use using utc utc_tmestamp view when where while with admin authorization char compact compactions conf cube current current_date current_timestamp day decimal defined dependency directories elem_type exchange file following for grouping hour ignore inner interval jar less logical macro minute month more none noscan over owner partialscan preceding pretty principals protection reload rewrite role roles rollup rows second server sets skewed transactions truncate unbounded unset uri user values window year"),
  builtin: set("bool boolean long timestamp tinyint smallint bigint int float double date datetime unsigned string array struct map uniontype key_type utctimestamp value_type varchar"),
  atoms: set("false true null unknown"),
  operatorChars: /^[*+\-%<>!=]/,
  dateSQL: set("date timestamp"),
  support: set("ODBCdotTable doubleQuote binaryNumber hexNumber")
});
var pgSQL = sql({
  client: set("source"),
  // For PostgreSQL - https://www.postgresql.org/docs/11/sql-keywords-appendix.html
  // For pl/pgsql lang - https://github.com/postgres/postgres/blob/REL_11_2/src/pl/plpgsql/src/pl_scanner.c
  keywords: set(sqlKeywords + "a abort abs absent absolute access according action ada add admin after aggregate alias all allocate also alter always analyse analyze and any are array array_agg array_max_cardinality as asc asensitive assert assertion assignment asymmetric at atomic attach attribute attributes authorization avg backward base64 before begin begin_frame begin_partition bernoulli between bigint binary bit bit_length blob blocked bom boolean both breadth by c cache call called cardinality cascade cascaded case cast catalog catalog_name ceil ceiling chain char char_length character character_length character_set_catalog character_set_name character_set_schema characteristics characters check checkpoint class class_origin clob close cluster coalesce cobol collate collation collation_catalog collation_name collation_schema collect column column_name columns command_function command_function_code comment comments commit committed concurrently condition condition_number configuration conflict connect connection connection_name constant constraint constraint_catalog constraint_name constraint_schema constraints constructor contains content continue control conversion convert copy corr corresponding cost count covar_pop covar_samp create cross csv cube cume_dist current current_catalog current_date current_default_transform_group current_path current_role current_row current_schema current_time current_timestamp current_transform_group_for_type current_user cursor cursor_name cycle data database datalink datatype date datetime_interval_code datetime_interval_precision day db deallocate debug dec decimal declare default defaults deferrable deferred defined definer degree delete delimiter delimiters dense_rank depends depth deref derived desc describe descriptor detach detail deterministic diagnostics dictionary disable discard disconnect dispatch distinct dlnewcopy dlpreviouscopy dlurlcomplete dlurlcompleteonly dlurlcompletewrite dlurlpath dlurlpathonly dlurlpathwrite dlurlscheme dlurlserver dlvalue do document domain double drop dump dynamic dynamic_function dynamic_function_code each element else elseif elsif empty enable encoding encrypted end end_frame end_partition endexec enforced enum equals errcode error escape event every except exception exclude excluding exclusive exec execute exists exit exp explain expression extension external extract false family fetch file filter final first first_value flag float floor following for force foreach foreign fortran forward found frame_row free freeze from fs full function functions fusion g general generated get global go goto grant granted greatest group grouping groups handler having header hex hierarchy hint hold hour id identity if ignore ilike immediate immediately immutable implementation implicit import in include including increment indent index indexes indicator info inherit inherits initially inline inner inout input insensitive insert instance instantiable instead int integer integrity intersect intersection interval into invoker is isnull isolation join k key key_member key_type label lag language large last last_value lateral lead leading leakproof least left length level library like like_regex limit link listen ln load local localtime localtimestamp location locator lock locked log logged loop lower m map mapping match matched materialized max max_cardinality maxvalue member merge message message_length message_octet_length message_text method min minute minvalue mod mode modifies module month more move multiset mumps name names namespace national natural nchar nclob nesting new next nfc nfd nfkc nfkd nil no none normalize normalized not nothing notice notify notnull nowait nth_value ntile null nullable nullif nulls number numeric object occurrences_regex octet_length octets of off offset oids old on only open operator option options or order ordering ordinality others out outer output over overlaps overlay overriding owned owner p pad parallel parameter parameter_mode parameter_name parameter_ordinal_position parameter_specific_catalog parameter_specific_name parameter_specific_schema parser partial partition pascal passing passthrough password path percent percent_rank percentile_cont percentile_disc perform period permission pg_context pg_datatype_name pg_exception_context pg_exception_detail pg_exception_hint placing plans pli policy portion position position_regex power precedes preceding precision prepare prepared preserve primary print_strict_params prior privileges procedural procedure procedures program public publication query quote raise range rank read reads real reassign recheck recovery recursive ref references referencing refresh regr_avgx regr_avgy regr_count regr_intercept regr_r2 regr_slope regr_sxx regr_sxy regr_syy reindex relative release rename repeatable replace replica requiring reset respect restart restore restrict result result_oid return returned_cardinality returned_length returned_octet_length returned_sqlstate returning returns reverse revoke right role rollback rollup routine routine_catalog routine_name routine_schema routines row row_count row_number rows rowtype rule savepoint scale schema schema_name schemas scope scope_catalog scope_name scope_schema scroll search second section security select selective self sensitive sequence sequences serializable server server_name session session_user set setof sets share show similar simple size skip slice smallint snapshot some source space specific specific_name specifictype sql sqlcode sqlerror sqlexception sqlstate sqlwarning sqrt stable stacked standalone start state statement static statistics stddev_pop stddev_samp stdin stdout storage strict strip structure style subclass_origin submultiset subscription substring substring_regex succeeds sum symmetric sysid system system_time system_user t table table_name tables tablesample tablespace temp template temporary text then ties time timestamp timezone_hour timezone_minute to token top_level_count trailing transaction transaction_active transactions_committed transactions_rolled_back transform transforms translate translate_regex translation treat trigger trigger_catalog trigger_name trigger_schema trim trim_array true truncate trusted type types uescape unbounded uncommitted under unencrypted union unique unknown unlink unlisten unlogged unnamed unnest until untyped update upper uri usage use_column use_variable user user_defined_type_catalog user_defined_type_code user_defined_type_name user_defined_type_schema using vacuum valid validate validator value value_of values var_pop var_samp varbinary varchar variable_conflict variadic varying verbose version versioning view views volatile warning when whenever where while whitespace width_bucket window with within without work wrapper write xml xmlagg xmlattributes xmlbinary xmlcast xmlcomment xmlconcat xmldeclaration xmldocument xmlelement xmlexists xmlforest xmliterate xmlnamespaces xmlparse xmlpi xmlquery xmlroot xmlschema xmlserialize xmltable xmltext xmlvalidate year yes zone"),
  // https://www.postgresql.org/docs/11/datatype.html
  builtin: set("bigint int8 bigserial serial8 bit varying varbit boolean bool box bytea character char varchar cidr circle date double precision float8 inet integer int int4 interval json jsonb line lseg macaddr macaddr8 money numeric decimal path pg_lsn point polygon real float4 smallint int2 smallserial serial2 serial serial4 text time without zone with timetz timestamp timestamptz tsquery tsvector txid_snapshot uuid xml"),
  atoms: set("false true null unknown"),
  operatorChars: /^[*\/+\-%<>!=&|^\/#@?~]/,
  backslashStringEscapes: false,
  dateSQL: set("date time timestamp"),
  support: set("ODBCdotTable decimallessFloat zerolessFloat binaryNumber hexNumber nCharCast charsetCast escapeConstant")
});
var gql = sql({
  keywords: set("ancestor and asc by contains desc descendant distinct from group has in is limit offset on order select superset where"),
  atoms: set("false true"),
  builtin: set("blob datetime first key __key__ string integer double boolean null"),
  operatorChars: /^[*+\-%<>!=]/
});
var gpSQL = sql({
  client: set("source"),
  //https://github.com/greenplum-db/gpdb/blob/master/src/include/parser/kwlist.h
  keywords: set("abort absolute access action active add admin after aggregate all also alter always analyse analyze and any array as asc assertion assignment asymmetric at authorization backward before begin between bigint binary bit boolean both by cache called cascade cascaded case cast chain char character characteristics check checkpoint class close cluster coalesce codegen collate column comment commit committed concurrency concurrently configuration connection constraint constraints contains content continue conversion copy cost cpu_rate_limit create createdb createexttable createrole createuser cross csv cube current current_catalog current_date current_role current_schema current_time current_timestamp current_user cursor cycle data database day deallocate dec decimal declare decode default defaults deferrable deferred definer delete delimiter delimiters deny desc dictionary disable discard distinct distributed do document domain double drop dxl each else enable encoding encrypted end enum errors escape every except exchange exclude excluding exclusive execute exists explain extension external extract false family fetch fields filespace fill filter first float following for force foreign format forward freeze from full function global grant granted greatest group group_id grouping handler hash having header hold host hour identity if ignore ilike immediate immutable implicit in including inclusive increment index indexes inherit inherits initially inline inner inout input insensitive insert instead int integer intersect interval into invoker is isnull isolation join key language large last leading least left level like limit list listen load local localtime localtimestamp location lock log login mapping master match maxvalue median merge minute minvalue missing mode modifies modify month move name names national natural nchar new newline next no nocreatedb nocreateexttable nocreaterole nocreateuser noinherit nologin none noovercommit nosuperuser not nothing notify notnull nowait null nullif nulls numeric object of off offset oids old on only operator option options or order ordered others out outer over overcommit overlaps overlay owned owner parser partial partition partitions passing password percent percentile_cont percentile_disc placing plans position preceding precision prepare prepared preserve primary prior privileges procedural procedure protocol queue quote randomly range read readable reads real reassign recheck recursive ref references reindex reject relative release rename repeatable replace replica reset resource restart restrict returning returns revoke right role rollback rollup rootpartition row rows rule savepoint scatter schema scroll search second security segment select sequence serializable session session_user set setof sets share show similar simple smallint some split sql stable standalone start statement statistics stdin stdout storage strict strip subpartition subpartitions substring superuser symmetric sysid system table tablespace temp template temporary text then threshold ties time timestamp to trailing transaction treat trigger trim true truncate trusted type unbounded uncommitted unencrypted union unique unknown unlisten until update user using vacuum valid validation validator value values varchar variadic varying verbose version view volatile web when where whitespace window with within without work writable write xml xmlattributes xmlconcat xmlelement xmlexists xmlforest xmlparse xmlpi xmlroot xmlserialize year yes zone"),
  builtin: set("bigint int8 bigserial serial8 bit varying varbit boolean bool box bytea character char varchar cidr circle date double precision float float8 inet integer int int4 interval json jsonb line lseg macaddr macaddr8 money numeric decimal path pg_lsn point polygon real float4 smallint int2 smallserial serial2 serial serial4 text time without zone with timetz timestamp timestamptz tsquery tsvector txid_snapshot uuid xml"),
  atoms: set("false true null unknown"),
  operatorChars: /^[*+\-%<>!=&|^\/#@?~]/,
  dateSQL: set("date time timestamp"),
  support: set("ODBCdotTable decimallessFloat zerolessFloat binaryNumber hexNumber nCharCast charsetCast")
});
var sparkSQL = sql({
  keywords: set("add after all alter analyze and anti archive array as asc at between bucket buckets by cache cascade case cast change clear cluster clustered codegen collection column columns comment commit compact compactions compute concatenate cost create cross cube current current_date current_timestamp database databases data dbproperties defined delete delimited deny desc describe dfs directories distinct distribute drop else end escaped except exchange exists explain export extended external false fields fileformat first following for format formatted from full function functions global grant group grouping having if ignore import in index indexes inner inpath inputformat insert intersect interval into is items join keys last lateral lazy left like limit lines list load local location lock locks logical macro map minus msck natural no not null nulls of on optimize option options or order out outer outputformat over overwrite partition partitioned partitions percent preceding principals purge range recordreader recordwriter recover reduce refresh regexp rename repair replace reset restrict revoke right rlike role roles rollback rollup row rows schema schemas select semi separated serde serdeproperties set sets show skewed sort sorted start statistics stored stratify struct table tables tablesample tblproperties temp temporary terminated then to touch transaction transactions transform true truncate unarchive unbounded uncache union unlock unset use using values view when where window with"),
  builtin: set("tinyint smallint int bigint boolean float double string binary timestamp decimal array map struct uniontype delimited serde sequencefile textfile rcfile inputformat outputformat"),
  atoms: set("false true null"),
  operatorChars: /^[*\/+\-%<>!=~&|^]/,
  dateSQL: set("date time timestamp"),
  support: set("ODBCdotTable doubleQuote zerolessFloat")
});
var esper = sql({
  client: set("source"),
  // http://www.espertech.com/esper/release-5.5.0/esper-reference/html/appendix_keywords.html
  keywords: set("alter and as asc between by count create delete desc distinct drop from group having in insert into is join like not on or order select set table union update values where limit after all and as at asc avedev avg between by case cast coalesce count create current_timestamp day days delete define desc distinct else end escape events every exists false first from full group having hour hours in inner insert instanceof into irstream is istream join last lastweekday left limit like max match_recognize matches median measures metadatasql min minute minutes msec millisecond milliseconds not null offset on or order outer output partition pattern prev prior regexp retain-union retain-intersection right rstream sec second seconds select set some snapshot sql stddev sum then true unidirectional until update variable weekday when where window"),
  builtin: {},
  atoms: set("false true null"),
  operatorChars: /^[*+\-%<>!=&|^\/#@?~]/,
  dateSQL: set("time"),
  support: set("decimallessFloat zerolessFloat binaryNumber hexNumber")
});

// node_modules/@codemirror/legacy-modes/mode/stex.js
function mkStex(mathMode) {
  function pushCommand(state, command) {
    state.cmdState.push(command);
  }
  function peekCommand(state) {
    if (state.cmdState.length > 0) {
      return state.cmdState[state.cmdState.length - 1];
    } else {
      return null;
    }
  }
  function popCommand(state) {
    var plug = state.cmdState.pop();
    if (plug) {
      plug.closeBracket();
    }
  }
  function getMostPowerful(state) {
    var context = state.cmdState;
    for (var i = context.length - 1; i >= 0; i--) {
      var plug = context[i];
      if (plug.name == "DEFAULT") {
        continue;
      }
      return plug;
    }
    return { styleIdentifier: function() {
      return null;
    } };
  }
  function addPluginPattern(pluginName, cmdStyle, styles) {
    return function() {
      this.name = pluginName;
      this.bracketNo = 0;
      this.style = cmdStyle;
      this.styles = styles;
      this.argument = null;
      this.styleIdentifier = function() {
        return this.styles[this.bracketNo - 1] || null;
      };
      this.openBracket = function() {
        this.bracketNo++;
        return "bracket";
      };
      this.closeBracket = function() {
      };
    };
  }
  var plugins = {};
  plugins["importmodule"] = addPluginPattern("importmodule", "tag", ["string", "builtin"]);
  plugins["documentclass"] = addPluginPattern("documentclass", "tag", ["", "atom"]);
  plugins["usepackage"] = addPluginPattern("usepackage", "tag", ["atom"]);
  plugins["begin"] = addPluginPattern("begin", "tag", ["atom"]);
  plugins["end"] = addPluginPattern("end", "tag", ["atom"]);
  plugins["label"] = addPluginPattern("label", "tag", ["atom"]);
  plugins["ref"] = addPluginPattern("ref", "tag", ["atom"]);
  plugins["eqref"] = addPluginPattern("eqref", "tag", ["atom"]);
  plugins["cite"] = addPluginPattern("cite", "tag", ["atom"]);
  plugins["bibitem"] = addPluginPattern("bibitem", "tag", ["atom"]);
  plugins["Bibitem"] = addPluginPattern("Bibitem", "tag", ["atom"]);
  plugins["RBibitem"] = addPluginPattern("RBibitem", "tag", ["atom"]);
  plugins["DEFAULT"] = function() {
    this.name = "DEFAULT";
    this.style = "tag";
    this.styleIdentifier = this.openBracket = this.closeBracket = function() {
    };
  };
  function setState(state, f) {
    state.f = f;
  }
  function normal3(source, state) {
    var plug;
    if (source.match(/^\\[a-zA-Z@\xc0-\u1fff\u2060-\uffff]+/)) {
      var cmdName = source.current().slice(1);
      plug = plugins.hasOwnProperty(cmdName) ? plugins[cmdName] : plugins["DEFAULT"];
      plug = new plug();
      pushCommand(state, plug);
      setState(state, beginParams);
      return plug.style;
    }
    if (source.match(/^\\[$&%#{}_]/)) {
      return "tag";
    }
    if (source.match(/^\\[,;!\/\\]/)) {
      return "tag";
    }
    if (source.match("\\[")) {
      setState(state, function(source2, state2) {
        return inMathMode(source2, state2, "\\]");
      });
      return "keyword";
    }
    if (source.match("\\(")) {
      setState(state, function(source2, state2) {
        return inMathMode(source2, state2, "\\)");
      });
      return "keyword";
    }
    if (source.match("$$")) {
      setState(state, function(source2, state2) {
        return inMathMode(source2, state2, "$$");
      });
      return "keyword";
    }
    if (source.match("$")) {
      setState(state, function(source2, state2) {
        return inMathMode(source2, state2, "$");
      });
      return "keyword";
    }
    var ch = source.next();
    if (ch == "%") {
      source.skipToEnd();
      return "comment";
    } else if (ch == "}" || ch == "]") {
      plug = peekCommand(state);
      if (plug) {
        plug.closeBracket(ch);
        setState(state, beginParams);
      } else {
        return "error";
      }
      return "bracket";
    } else if (ch == "{" || ch == "[") {
      plug = plugins["DEFAULT"];
      plug = new plug();
      pushCommand(state, plug);
      return "bracket";
    } else if (/\d/.test(ch)) {
      source.eatWhile(/[\w.%]/);
      return "atom";
    } else {
      source.eatWhile(/[\w\-_]/);
      plug = getMostPowerful(state);
      if (plug.name == "begin") {
        plug.argument = source.current();
      }
      return plug.styleIdentifier();
    }
  }
  function inMathMode(source, state, endModeSeq) {
    if (source.eatSpace()) {
      return null;
    }
    if (endModeSeq && source.match(endModeSeq)) {
      setState(state, normal3);
      return "keyword";
    }
    if (source.match(/^\\[a-zA-Z@]+/)) {
      return "tag";
    }
    if (source.match(/^[a-zA-Z]+/)) {
      return "variableName.special";
    }
    if (source.match(/^\\[$&%#{}_]/)) {
      return "tag";
    }
    if (source.match(/^\\[,;!\/]/)) {
      return "tag";
    }
    if (source.match(/^[\^_&]/)) {
      return "tag";
    }
    if (source.match(/^[+\-<>|=,\/@!*:;'"`~#?]/)) {
      return null;
    }
    if (source.match(/^(\d+\.\d*|\d*\.\d+|\d+)/)) {
      return "number";
    }
    var ch = source.next();
    if (ch == "{" || ch == "}" || ch == "[" || ch == "]" || ch == "(" || ch == ")") {
      return "bracket";
    }
    if (ch == "%") {
      source.skipToEnd();
      return "comment";
    }
    return "error";
  }
  function beginParams(source, state) {
    var ch = source.peek(), lastPlug;
    if (ch == "{" || ch == "[") {
      lastPlug = peekCommand(state);
      lastPlug.openBracket(ch);
      source.eat(ch);
      setState(state, normal3);
      return "bracket";
    }
    if (/[ \t\r]/.test(ch)) {
      source.eat(ch);
      return null;
    }
    setState(state, normal3);
    popCommand(state);
    return normal3(source, state);
  }
  return {
    name: "stex",
    startState: function() {
      var f = mathMode ? function(source, state) {
        return inMathMode(source, state);
      } : normal3;
      return {
        cmdState: [],
        f
      };
    },
    copyState: function(s) {
      return {
        cmdState: s.cmdState.slice(),
        f: s.f
      };
    },
    token: function(stream, state) {
      return state.f(stream, state);
    },
    blankLine: function(state) {
      state.f = normal3;
      state.cmdState.length = 0;
    },
    languageData: {
      commentTokens: { line: "%" }
    }
  };
}
var stex = mkStex(false);
var stexMath = mkStex(true);

// node_modules/@codemirror/legacy-modes/mode/swift.js
function wordSet(words8) {
  var set2 = {};
  for (var i = 0; i < words8.length; i++) set2[words8[i]] = true;
  return set2;
}
var keywords12 = wordSet([
  "_",
  "var",
  "let",
  "actor",
  "class",
  "enum",
  "extension",
  "import",
  "protocol",
  "struct",
  "func",
  "typealias",
  "associatedtype",
  "open",
  "public",
  "internal",
  "fileprivate",
  "private",
  "deinit",
  "init",
  "new",
  "override",
  "self",
  "subscript",
  "super",
  "convenience",
  "dynamic",
  "final",
  "indirect",
  "lazy",
  "required",
  "static",
  "unowned",
  "unowned(safe)",
  "unowned(unsafe)",
  "weak",
  "as",
  "is",
  "break",
  "case",
  "continue",
  "default",
  "else",
  "fallthrough",
  "for",
  "guard",
  "if",
  "in",
  "repeat",
  "switch",
  "where",
  "while",
  "defer",
  "return",
  "inout",
  "mutating",
  "nonmutating",
  "isolated",
  "nonisolated",
  "catch",
  "do",
  "rethrows",
  "throw",
  "throws",
  "async",
  "await",
  "try",
  "didSet",
  "get",
  "set",
  "willSet",
  "assignment",
  "associativity",
  "infix",
  "left",
  "none",
  "operator",
  "postfix",
  "precedence",
  "precedencegroup",
  "prefix",
  "right",
  "Any",
  "AnyObject",
  "Type",
  "dynamicType",
  "Self",
  "Protocol",
  "__COLUMN__",
  "__FILE__",
  "__FUNCTION__",
  "__LINE__"
]);
var definingKeywords = wordSet(["var", "let", "actor", "class", "enum", "extension", "import", "protocol", "struct", "func", "typealias", "associatedtype", "for"]);
var atoms5 = wordSet(["true", "false", "nil", "self", "super", "_"]);
var types = wordSet([
  "Array",
  "Bool",
  "Character",
  "Dictionary",
  "Double",
  "Float",
  "Int",
  "Int8",
  "Int16",
  "Int32",
  "Int64",
  "Never",
  "Optional",
  "Set",
  "String",
  "UInt8",
  "UInt16",
  "UInt32",
  "UInt64",
  "Void"
]);
var operators3 = "+-/*%=|&<>~^?!";
var punc = ":;,.(){}[]";
var binary = /^\-?0b[01][01_]*/;
var octal = /^\-?0o[0-7][0-7_]*/;
var hexadecimal = /^\-?0x[\dA-Fa-f][\dA-Fa-f_]*(?:(?:\.[\dA-Fa-f][\dA-Fa-f_]*)?[Pp]\-?\d[\d_]*)?/;
var decimal = /^\-?\d[\d_]*(?:\.\d[\d_]*)?(?:[Ee]\-?\d[\d_]*)?/;
var identifier = /^\$\d+|(`?)[_A-Za-z][_A-Za-z$0-9]*\1/;
var property = /^\.(?:\$\d+|(`?)[_A-Za-z][_A-Za-z$0-9]*\1)/;
var instruction = /^\#[A-Za-z]+/;
var attribute = /^@(?:\$\d+|(`?)[_A-Za-z][_A-Za-z$0-9]*\1)/;
function tokenBase11(stream, state, prev) {
  if (stream.sol()) state.indented = stream.indentation();
  if (stream.eatSpace()) return null;
  var ch = stream.peek();
  if (ch == "/") {
    if (stream.match("//")) {
      stream.skipToEnd();
      return "comment";
    }
    if (stream.match("/*")) {
      state.tokenize.push(tokenComment6);
      return tokenComment6(stream, state);
    }
  }
  if (stream.match(instruction)) return "builtin";
  if (stream.match(attribute)) return "attribute";
  if (stream.match(binary)) return "number";
  if (stream.match(octal)) return "number";
  if (stream.match(hexadecimal)) return "number";
  if (stream.match(decimal)) return "number";
  if (stream.match(property)) return "property";
  if (operators3.indexOf(ch) > -1) {
    stream.next();
    return "operator";
  }
  if (punc.indexOf(ch) > -1) {
    stream.next();
    stream.match("..");
    return "punctuation";
  }
  var stringMatch;
  if (stringMatch = stream.match(/("""|"|')/)) {
    var tokenize3 = tokenString7.bind(null, stringMatch[0]);
    state.tokenize.push(tokenize3);
    return tokenize3(stream, state);
  }
  if (stream.match(identifier)) {
    var ident = stream.current();
    if (types.hasOwnProperty(ident)) return "type";
    if (atoms5.hasOwnProperty(ident)) return "atom";
    if (keywords12.hasOwnProperty(ident)) {
      if (definingKeywords.hasOwnProperty(ident))
        state.prev = "define";
      return "keyword";
    }
    if (prev == "define") return "def";
    return "variable";
  }
  stream.next();
  return null;
}
function tokenUntilClosingParen() {
  var depth = 0;
  return function(stream, state, prev) {
    var inner = tokenBase11(stream, state, prev);
    if (inner == "punctuation") {
      if (stream.current() == "(") ++depth;
      else if (stream.current() == ")") {
        if (depth == 0) {
          stream.backUp(1);
          state.tokenize.pop();
          return state.tokenize[state.tokenize.length - 1](stream, state);
        } else --depth;
      }
    }
    return inner;
  };
}
function tokenString7(openQuote, stream, state) {
  var singleLine = openQuote.length == 1;
  var ch, escaped = false;
  while (ch = stream.peek()) {
    if (escaped) {
      stream.next();
      if (ch == "(") {
        state.tokenize.push(tokenUntilClosingParen());
        return "string";
      }
      escaped = false;
    } else if (stream.match(openQuote)) {
      state.tokenize.pop();
      return "string";
    } else {
      stream.next();
      escaped = ch == "\\";
    }
  }
  if (singleLine) {
    state.tokenize.pop();
  }
  return "string";
}
function tokenComment6(stream, state) {
  var ch;
  while (ch = stream.next()) {
    if (ch === "/" && stream.eat("*")) {
      state.tokenize.push(tokenComment6);
    } else if (ch === "*" && stream.eat("/")) {
      state.tokenize.pop();
      break;
    }
  }
  return "comment";
}
function Context3(prev, align, indented) {
  this.prev = prev;
  this.align = align;
  this.indented = indented;
}
function pushContext3(state, stream) {
  var align = stream.match(/^\s*($|\/[\/\*]|[)}\]])/, false) ? null : stream.column() + 1;
  state.context = new Context3(state.context, align, state.indented);
}
function popContext3(state) {
  if (state.context) {
    state.indented = state.context.indented;
    state.context = state.context.prev;
  }
}
var swift = {
  name: "swift",
  startState: function() {
    return {
      prev: null,
      context: null,
      indented: 0,
      tokenize: []
    };
  },
  token: function(stream, state) {
    var prev = state.prev;
    state.prev = null;
    var tokenize3 = state.tokenize[state.tokenize.length - 1] || tokenBase11;
    var style = tokenize3(stream, state, prev);
    if (!style || style == "comment") state.prev = prev;
    else if (!state.prev) state.prev = style;
    if (style == "punctuation") {
      var bracket = /[\(\[\{]|([\]\)\}])/.exec(stream.current());
      if (bracket) (bracket[1] ? popContext3 : pushContext3)(state, stream);
    }
    return style;
  },
  indent: function(state, textAfter, iCx) {
    var cx = state.context;
    if (!cx) return 0;
    var closing3 = /^[\]\}\)]/.test(textAfter);
    if (cx.align != null) return cx.align - (closing3 ? 1 : 0);
    return cx.indented + (closing3 ? 0 : iCx.unit);
  },
  languageData: {
    indentOnInput: /^\s*[\)\}\]]$/,
    commentTokens: { line: "//", block: { open: "/*", close: "*/" } },
    closeBrackets: { brackets: ["(", "[", "{", "'", '"', "`"] }
  }
};

// node_modules/@codemirror/legacy-modes/mode/toml.js
var toml = {
  name: "toml",
  startState: function() {
    return {
      inString: false,
      stringType: "",
      lhs: true,
      inArray: 0
    };
  },
  token: function(stream, state) {
    if (!state.inString && (stream.peek() == '"' || stream.peek() == "'")) {
      state.stringType = stream.peek();
      stream.next();
      state.inString = true;
    }
    if (stream.sol() && state.inArray === 0) {
      state.lhs = true;
    }
    if (state.inString) {
      while (state.inString && !stream.eol()) {
        if (stream.peek() === state.stringType) {
          stream.next();
          state.inString = false;
        } else if (stream.peek() === "\\") {
          stream.next();
          stream.next();
        } else {
          stream.match(/^.[^\\\"\']*/);
        }
      }
      return state.lhs ? "property" : "string";
    } else if (state.inArray && stream.peek() === "]") {
      stream.next();
      state.inArray--;
      return "bracket";
    } else if (state.lhs && stream.peek() === "[" && stream.skipTo("]")) {
      stream.next();
      if (stream.peek() === "]") stream.next();
      return "atom";
    } else if (stream.peek() === "#") {
      stream.skipToEnd();
      return "comment";
    } else if (stream.eatSpace()) {
      return null;
    } else if (state.lhs && stream.eatWhile(function(c2) {
      return c2 != "=" && c2 != " ";
    })) {
      return "property";
    } else if (state.lhs && stream.peek() === "=") {
      stream.next();
      state.lhs = false;
      return null;
    } else if (!state.lhs && stream.match(/^\d\d\d\d[\d\-\:\.T]*Z/)) {
      return "atom";
    } else if (!state.lhs && (stream.match("true") || stream.match("false"))) {
      return "atom";
    } else if (!state.lhs && stream.peek() === "[") {
      state.inArray++;
      stream.next();
      return "bracket";
    } else if (!state.lhs && stream.match(/^\-?\d+(?:\.\d+)?/)) {
      return "number";
    } else if (!stream.eatSpace()) {
      stream.next();
    }
    return null;
  },
  languageData: {
    commentTokens: { line: "#" }
  }
};

// node_modules/@codemirror/legacy-modes/mode/vb.js
var ERRORCLASS = "error";
function wordRegexp4(words8) {
  return new RegExp("^((" + words8.join(")|(") + "))\\b", "i");
}
var singleOperators2 = new RegExp("^[\\+\\-\\*/%&\\\\|\\^~<>!]");
var singleDelimiters2 = new RegExp("^[\\(\\)\\[\\]\\{\\}@,:`=;\\.]");
var doubleOperators2 = new RegExp("^((==)|(<>)|(<=)|(>=)|(<>)|(<<)|(>>)|(//)|(\\*\\*))");
var doubleDelimiters2 = new RegExp("^((\\+=)|(\\-=)|(\\*=)|(%=)|(/=)|(&=)|(\\|=)|(\\^=))");
var tripleDelimiters2 = new RegExp("^((//=)|(>>=)|(<<=)|(\\*\\*=))");
var identifiers4 = new RegExp("^[_A-Za-z][_A-Za-z0-9]*");
var openingKeywords = ["class", "module", "sub", "enum", "select", "while", "if", "function", "get", "set", "property", "try", "structure", "synclock", "using", "with"];
var middleKeywords = ["else", "elseif", "case", "catch", "finally"];
var endKeywords = ["next", "loop"];
var operatorKeywords = ["and", "andalso", "or", "orelse", "xor", "in", "not", "is", "isnot", "like"];
var wordOperators3 = wordRegexp4(operatorKeywords);
var commonKeywords4 = ["#const", "#else", "#elseif", "#end", "#if", "#region", "addhandler", "addressof", "alias", "as", "byref", "byval", "cbool", "cbyte", "cchar", "cdate", "cdbl", "cdec", "cint", "clng", "cobj", "compare", "const", "continue", "csbyte", "cshort", "csng", "cstr", "cuint", "culng", "cushort", "declare", "default", "delegate", "dim", "directcast", "each", "erase", "error", "event", "exit", "explicit", "false", "for", "friend", "gettype", "goto", "handles", "implements", "imports", "infer", "inherits", "interface", "isfalse", "istrue", "lib", "me", "mod", "mustinherit", "mustoverride", "my", "mybase", "myclass", "namespace", "narrowing", "new", "nothing", "notinheritable", "notoverridable", "of", "off", "on", "operator", "option", "optional", "out", "overloads", "overridable", "overrides", "paramarray", "partial", "private", "protected", "public", "raiseevent", "readonly", "redim", "removehandler", "resume", "return", "shadows", "shared", "static", "step", "stop", "strict", "then", "throw", "to", "true", "trycast", "typeof", "until", "until", "when", "widening", "withevents", "writeonly"];
var commontypes = ["object", "boolean", "char", "string", "byte", "sbyte", "short", "ushort", "int16", "uint16", "integer", "uinteger", "int32", "uint32", "long", "ulong", "int64", "uint64", "decimal", "single", "double", "float", "date", "datetime", "intptr", "uintptr"];
var keywords13 = wordRegexp4(commonKeywords4);
var types2 = wordRegexp4(commontypes);
var stringPrefixes2 = '"';
var opening2 = wordRegexp4(openingKeywords);
var middle = wordRegexp4(middleKeywords);
var closing2 = wordRegexp4(endKeywords);
var doubleClosing = wordRegexp4(["end"]);
var doOpening = wordRegexp4(["do"]);
var indentInfo = null;
function indent(_stream, state) {
  state.currentIndent++;
}
function dedent(_stream, state) {
  state.currentIndent--;
}
function tokenBase12(stream, state) {
  if (stream.eatSpace()) {
    return null;
  }
  var ch = stream.peek();
  if (ch === "'") {
    stream.skipToEnd();
    return "comment";
  }
  if (stream.match(/^((&H)|(&O))?[0-9\.a-f]/i, false)) {
    var floatLiteral = false;
    if (stream.match(/^\d*\.\d+F?/i)) {
      floatLiteral = true;
    } else if (stream.match(/^\d+\.\d*F?/)) {
      floatLiteral = true;
    } else if (stream.match(/^\.\d+F?/)) {
      floatLiteral = true;
    }
    if (floatLiteral) {
      stream.eat(/J/i);
      return "number";
    }
    var intLiteral = false;
    if (stream.match(/^&H[0-9a-f]+/i)) {
      intLiteral = true;
    } else if (stream.match(/^&O[0-7]+/i)) {
      intLiteral = true;
    } else if (stream.match(/^[1-9]\d*F?/)) {
      stream.eat(/J/i);
      intLiteral = true;
    } else if (stream.match(/^0(?![\dx])/i)) {
      intLiteral = true;
    }
    if (intLiteral) {
      stream.eat(/L/i);
      return "number";
    }
  }
  if (stream.match(stringPrefixes2)) {
    state.tokenize = tokenStringFactory2(stream.current());
    return state.tokenize(stream, state);
  }
  if (stream.match(tripleDelimiters2) || stream.match(doubleDelimiters2)) {
    return null;
  }
  if (stream.match(doubleOperators2) || stream.match(singleOperators2) || stream.match(wordOperators3)) {
    return "operator";
  }
  if (stream.match(singleDelimiters2)) {
    return null;
  }
  if (stream.match(doOpening)) {
    indent(stream, state);
    state.doInCurrentLine = true;
    return "keyword";
  }
  if (stream.match(opening2)) {
    if (!state.doInCurrentLine)
      indent(stream, state);
    else
      state.doInCurrentLine = false;
    return "keyword";
  }
  if (stream.match(middle)) {
    return "keyword";
  }
  if (stream.match(doubleClosing)) {
    dedent(stream, state);
    dedent(stream, state);
    return "keyword";
  }
  if (stream.match(closing2)) {
    dedent(stream, state);
    return "keyword";
  }
  if (stream.match(types2)) {
    return "keyword";
  }
  if (stream.match(keywords13)) {
    return "keyword";
  }
  if (stream.match(identifiers4)) {
    return "variable";
  }
  stream.next();
  return ERRORCLASS;
}
function tokenStringFactory2(delimiter2) {
  var singleline = delimiter2.length == 1;
  var OUTCLASS = "string";
  return function(stream, state) {
    while (!stream.eol()) {
      stream.eatWhile(/[^'"]/);
      if (stream.match(delimiter2)) {
        state.tokenize = tokenBase12;
        return OUTCLASS;
      } else {
        stream.eat(/['"]/);
      }
    }
    if (singleline) {
      state.tokenize = tokenBase12;
    }
    return OUTCLASS;
  };
}
function tokenLexer(stream, state) {
  var style = state.tokenize(stream, state);
  var current = stream.current();
  if (current === ".") {
    style = state.tokenize(stream, state);
    if (style === "variable") {
      return "variable";
    } else {
      return ERRORCLASS;
    }
  }
  var delimiter_index = "[({".indexOf(current);
  if (delimiter_index !== -1) {
    indent(stream, state);
  }
  if (indentInfo === "dedent") {
    if (dedent(stream, state)) {
      return ERRORCLASS;
    }
  }
  delimiter_index = "])}".indexOf(current);
  if (delimiter_index !== -1) {
    if (dedent(stream, state)) {
      return ERRORCLASS;
    }
  }
  return style;
}
var vb = {
  name: "vb",
  startState: function() {
    return {
      tokenize: tokenBase12,
      lastToken: null,
      currentIndent: 0,
      nextLineIndent: 0,
      doInCurrentLine: false
    };
  },
  token: function(stream, state) {
    if (stream.sol()) {
      state.currentIndent += state.nextLineIndent;
      state.nextLineIndent = 0;
      state.doInCurrentLine = 0;
    }
    var style = tokenLexer(stream, state);
    state.lastToken = { style, content: stream.current() };
    return style;
  },
  indent: function(state, textAfter, cx) {
    var trueText = textAfter.replace(/^\s+|\s+$/g, "");
    if (trueText.match(closing2) || trueText.match(doubleClosing) || trueText.match(middle)) return cx.unit * (state.currentIndent - 1);
    if (state.currentIndent < 0) return 0;
    return state.currentIndent * cx.unit;
  },
  languageData: {
    closeBrackets: { brackets: ["(", "[", "{", '"'] },
    commentTokens: { line: "'" },
    autocomplete: openingKeywords.concat(middleKeywords).concat(endKeywords).concat(operatorKeywords).concat(commonKeywords4).concat(commontypes)
  }
};

// node_modules/@codemirror/legacy-modes/mode/vbscript.js
function mkVBScript(parserConf) {
  var ERRORCLASS2 = "error";
  function wordRegexp5(words8) {
    return new RegExp("^((" + words8.join(")|(") + "))\\b", "i");
  }
  var singleOperators3 = new RegExp("^[\\+\\-\\*/&\\\\\\^<>=]");
  var doubleOperators3 = new RegExp("^((<>)|(<=)|(>=))");
  var singleDelimiters3 = new RegExp("^[\\.,]");
  var brackets = new RegExp("^[\\(\\)]");
  var identifiers5 = new RegExp("^[A-Za-z][_A-Za-z0-9]*");
  var openingKeywords2 = ["class", "sub", "select", "while", "if", "function", "property", "with", "for"];
  var middleKeywords2 = ["else", "elseif", "case"];
  var endKeywords2 = ["next", "loop", "wend"];
  var wordOperators4 = wordRegexp5(["and", "or", "not", "xor", "is", "mod", "eqv", "imp"]);
  var commonkeywords = [
    "dim",
    "redim",
    "then",
    "until",
    "randomize",
    "byval",
    "byref",
    "new",
    "property",
    "exit",
    "in",
    "const",
    "private",
    "public",
    "get",
    "set",
    "let",
    "stop",
    "on error resume next",
    "on error goto 0",
    "option explicit",
    "call",
    "me"
  ];
  var atomWords = ["true", "false", "nothing", "empty", "null"];
  var builtinFuncsWords = [
    "abs",
    "array",
    "asc",
    "atn",
    "cbool",
    "cbyte",
    "ccur",
    "cdate",
    "cdbl",
    "chr",
    "cint",
    "clng",
    "cos",
    "csng",
    "cstr",
    "date",
    "dateadd",
    "datediff",
    "datepart",
    "dateserial",
    "datevalue",
    "day",
    "escape",
    "eval",
    "execute",
    "exp",
    "filter",
    "formatcurrency",
    "formatdatetime",
    "formatnumber",
    "formatpercent",
    "getlocale",
    "getobject",
    "getref",
    "hex",
    "hour",
    "inputbox",
    "instr",
    "instrrev",
    "int",
    "fix",
    "isarray",
    "isdate",
    "isempty",
    "isnull",
    "isnumeric",
    "isobject",
    "join",
    "lbound",
    "lcase",
    "left",
    "len",
    "loadpicture",
    "log",
    "ltrim",
    "rtrim",
    "trim",
    "maths",
    "mid",
    "minute",
    "month",
    "monthname",
    "msgbox",
    "now",
    "oct",
    "replace",
    "rgb",
    "right",
    "rnd",
    "round",
    "scriptengine",
    "scriptenginebuildversion",
    "scriptenginemajorversion",
    "scriptengineminorversion",
    "second",
    "setlocale",
    "sgn",
    "sin",
    "space",
    "split",
    "sqr",
    "strcomp",
    "string",
    "strreverse",
    "tan",
    "time",
    "timer",
    "timeserial",
    "timevalue",
    "typename",
    "ubound",
    "ucase",
    "unescape",
    "vartype",
    "weekday",
    "weekdayname",
    "year"
  ];
  var builtinConsts = [
    "vbBlack",
    "vbRed",
    "vbGreen",
    "vbYellow",
    "vbBlue",
    "vbMagenta",
    "vbCyan",
    "vbWhite",
    "vbBinaryCompare",
    "vbTextCompare",
    "vbSunday",
    "vbMonday",
    "vbTuesday",
    "vbWednesday",
    "vbThursday",
    "vbFriday",
    "vbSaturday",
    "vbUseSystemDayOfWeek",
    "vbFirstJan1",
    "vbFirstFourDays",
    "vbFirstFullWeek",
    "vbGeneralDate",
    "vbLongDate",
    "vbShortDate",
    "vbLongTime",
    "vbShortTime",
    "vbObjectError",
    "vbOKOnly",
    "vbOKCancel",
    "vbAbortRetryIgnore",
    "vbYesNoCancel",
    "vbYesNo",
    "vbRetryCancel",
    "vbCritical",
    "vbQuestion",
    "vbExclamation",
    "vbInformation",
    "vbDefaultButton1",
    "vbDefaultButton2",
    "vbDefaultButton3",
    "vbDefaultButton4",
    "vbApplicationModal",
    "vbSystemModal",
    "vbOK",
    "vbCancel",
    "vbAbort",
    "vbRetry",
    "vbIgnore",
    "vbYes",
    "vbNo",
    "vbCr",
    "VbCrLf",
    "vbFormFeed",
    "vbLf",
    "vbNewLine",
    "vbNullChar",
    "vbNullString",
    "vbTab",
    "vbVerticalTab",
    "vbUseDefault",
    "vbTrue",
    "vbFalse",
    "vbEmpty",
    "vbNull",
    "vbInteger",
    "vbLong",
    "vbSingle",
    "vbDouble",
    "vbCurrency",
    "vbDate",
    "vbString",
    "vbObject",
    "vbError",
    "vbBoolean",
    "vbVariant",
    "vbDataObject",
    "vbDecimal",
    "vbByte",
    "vbArray"
  ];
  var builtinObjsWords = ["WScript", "err", "debug", "RegExp"];
  var knownProperties = ["description", "firstindex", "global", "helpcontext", "helpfile", "ignorecase", "length", "number", "pattern", "source", "value", "count"];
  var knownMethods = ["clear", "execute", "raise", "replace", "test", "write", "writeline", "close", "open", "state", "eof", "update", "addnew", "end", "createobject", "quit"];
  var aspBuiltinObjsWords = ["server", "response", "request", "session", "application"];
  var aspKnownProperties = [
    "buffer",
    "cachecontrol",
    "charset",
    "contenttype",
    "expires",
    "expiresabsolute",
    "isclientconnected",
    "pics",
    "status",
    //response
    "clientcertificate",
    "cookies",
    "form",
    "querystring",
    "servervariables",
    "totalbytes",
    //request
    "contents",
    "staticobjects",
    //application
    "codepage",
    "lcid",
    "sessionid",
    "timeout",
    //session
    "scripttimeout"
  ];
  var aspKnownMethods = [
    "addheader",
    "appendtolog",
    "binarywrite",
    "end",
    "flush",
    "redirect",
    //response
    "binaryread",
    //request
    "remove",
    "removeall",
    "lock",
    "unlock",
    //application
    "abandon",
    //session
    "getlasterror",
    "htmlencode",
    "mappath",
    "transfer",
    "urlencode"
  ];
  var knownWords = knownMethods.concat(knownProperties);
  builtinObjsWords = builtinObjsWords.concat(builtinConsts);
  if (parserConf.isASP) {
    builtinObjsWords = builtinObjsWords.concat(aspBuiltinObjsWords);
    knownWords = knownWords.concat(aspKnownMethods, aspKnownProperties);
  }
  ;
  var keywords14 = wordRegexp5(commonkeywords);
  var atoms6 = wordRegexp5(atomWords);
  var builtinFuncs = wordRegexp5(builtinFuncsWords);
  var builtinObjs = wordRegexp5(builtinObjsWords);
  var known = wordRegexp5(knownWords);
  var stringPrefixes3 = '"';
  var opening3 = wordRegexp5(openingKeywords2);
  var middle2 = wordRegexp5(middleKeywords2);
  var closing3 = wordRegexp5(endKeywords2);
  var doubleClosing2 = wordRegexp5(["end"]);
  var doOpening2 = wordRegexp5(["do"]);
  var noIndentWords = wordRegexp5(["on error resume next", "exit"]);
  var comment = wordRegexp5(["rem"]);
  function indent2(_stream, state) {
    state.currentIndent++;
  }
  function dedent2(_stream, state) {
    state.currentIndent--;
  }
  function tokenBase13(stream, state) {
    if (stream.eatSpace()) {
      return null;
    }
    var ch = stream.peek();
    if (ch === "'") {
      stream.skipToEnd();
      return "comment";
    }
    if (stream.match(comment)) {
      stream.skipToEnd();
      return "comment";
    }
    if (stream.match(/^((&H)|(&O))?[0-9\.]/i, false) && !stream.match(/^((&H)|(&O))?[0-9\.]+[a-z_]/i, false)) {
      var floatLiteral = false;
      if (stream.match(/^\d*\.\d+/i)) {
        floatLiteral = true;
      } else if (stream.match(/^\d+\.\d*/)) {
        floatLiteral = true;
      } else if (stream.match(/^\.\d+/)) {
        floatLiteral = true;
      }
      if (floatLiteral) {
        stream.eat(/J/i);
        return "number";
      }
      var intLiteral = false;
      if (stream.match(/^&H[0-9a-f]+/i)) {
        intLiteral = true;
      } else if (stream.match(/^&O[0-7]+/i)) {
        intLiteral = true;
      } else if (stream.match(/^[1-9]\d*F?/)) {
        stream.eat(/J/i);
        intLiteral = true;
      } else if (stream.match(/^0(?![\dx])/i)) {
        intLiteral = true;
      }
      if (intLiteral) {
        stream.eat(/L/i);
        return "number";
      }
    }
    if (stream.match(stringPrefixes3)) {
      state.tokenize = tokenStringFactory3(stream.current());
      return state.tokenize(stream, state);
    }
    if (stream.match(doubleOperators3) || stream.match(singleOperators3) || stream.match(wordOperators4)) {
      return "operator";
    }
    if (stream.match(singleDelimiters3)) {
      return null;
    }
    if (stream.match(brackets)) {
      return "bracket";
    }
    if (stream.match(noIndentWords)) {
      state.doInCurrentLine = true;
      return "keyword";
    }
    if (stream.match(doOpening2)) {
      indent2(stream, state);
      state.doInCurrentLine = true;
      return "keyword";
    }
    if (stream.match(opening3)) {
      if (!state.doInCurrentLine)
        indent2(stream, state);
      else
        state.doInCurrentLine = false;
      return "keyword";
    }
    if (stream.match(middle2)) {
      return "keyword";
    }
    if (stream.match(doubleClosing2)) {
      dedent2(stream, state);
      dedent2(stream, state);
      return "keyword";
    }
    if (stream.match(closing3)) {
      if (!state.doInCurrentLine)
        dedent2(stream, state);
      else
        state.doInCurrentLine = false;
      return "keyword";
    }
    if (stream.match(keywords14)) {
      return "keyword";
    }
    if (stream.match(atoms6)) {
      return "atom";
    }
    if (stream.match(known)) {
      return "variableName.special";
    }
    if (stream.match(builtinFuncs)) {
      return "builtin";
    }
    if (stream.match(builtinObjs)) {
      return "builtin";
    }
    if (stream.match(identifiers5)) {
      return "variable";
    }
    stream.next();
    return ERRORCLASS2;
  }
  function tokenStringFactory3(delimiter2) {
    var singleline = delimiter2.length == 1;
    var OUTCLASS = "string";
    return function(stream, state) {
      while (!stream.eol()) {
        stream.eatWhile(/[^'"]/);
        if (stream.match(delimiter2)) {
          state.tokenize = tokenBase13;
          return OUTCLASS;
        } else {
          stream.eat(/['"]/);
        }
      }
      if (singleline) {
        state.tokenize = tokenBase13;
      }
      return OUTCLASS;
    };
  }
  function tokenLexer2(stream, state) {
    var style = state.tokenize(stream, state);
    var current = stream.current();
    if (current === ".") {
      style = state.tokenize(stream, state);
      current = stream.current();
      if (style && (style.substr(0, 8) === "variable" || style === "builtin" || style === "keyword")) {
        if (style === "builtin" || style === "keyword") style = "variable";
        if (knownWords.indexOf(current.substr(1)) > -1) style = "keyword";
        return style;
      } else {
        return ERRORCLASS2;
      }
    }
    return style;
  }
  return {
    name: "vbscript",
    startState: function() {
      return {
        tokenize: tokenBase13,
        lastToken: null,
        currentIndent: 0,
        nextLineIndent: 0,
        doInCurrentLine: false,
        ignoreKeyword: false
      };
    },
    token: function(stream, state) {
      if (stream.sol()) {
        state.currentIndent += state.nextLineIndent;
        state.nextLineIndent = 0;
        state.doInCurrentLine = 0;
      }
      var style = tokenLexer2(stream, state);
      state.lastToken = { style, content: stream.current() };
      if (style === null) style = null;
      return style;
    },
    indent: function(state, textAfter, cx) {
      var trueText = textAfter.replace(/^\s+|\s+$/g, "");
      if (trueText.match(closing3) || trueText.match(doubleClosing2) || trueText.match(middle2)) return cx.unit * (state.currentIndent - 1);
      if (state.currentIndent < 0) return 0;
      return state.currentIndent * cx.unit;
    }
  };
}
var vbScript = mkVBScript({});
var vbScriptASP = mkVBScript({ isASP: true });

// node_modules/@codemirror/legacy-modes/mode/yaml.js
var cons = ["true", "false", "on", "off", "yes", "no"];
var keywordRegex = new RegExp("\\b((" + cons.join(")|(") + "))$", "i");
var yaml = {
  name: "yaml",
  token: function(stream, state) {
    var ch = stream.peek();
    var esc = state.escaped;
    state.escaped = false;
    if (ch == "#" && (stream.pos == 0 || /\s/.test(stream.string.charAt(stream.pos - 1)))) {
      stream.skipToEnd();
      return "comment";
    }
    if (stream.match(/^('([^']|\\.)*'?|"([^"]|\\.)*"?)/))
      return "string";
    if (state.literal && stream.indentation() > state.keyCol) {
      stream.skipToEnd();
      return "string";
    } else if (state.literal) {
      state.literal = false;
    }
    if (stream.sol()) {
      state.keyCol = 0;
      state.pair = false;
      state.pairStart = false;
      if (stream.match("---")) {
        return "def";
      }
      if (stream.match("...")) {
        return "def";
      }
      if (stream.match(/^\s*-\s+/)) {
        return "meta";
      }
    }
    if (stream.match(/^(\{|\}|\[|\])/)) {
      if (ch == "{")
        state.inlinePairs++;
      else if (ch == "}")
        state.inlinePairs--;
      else if (ch == "[")
        state.inlineList++;
      else
        state.inlineList--;
      return "meta";
    }
    if (state.inlineList > 0 && !esc && ch == ",") {
      stream.next();
      return "meta";
    }
    if (state.inlinePairs > 0 && !esc && ch == ",") {
      state.keyCol = 0;
      state.pair = false;
      state.pairStart = false;
      stream.next();
      return "meta";
    }
    if (state.pairStart) {
      if (stream.match(/^\s*(\||\>)\s*/)) {
        state.literal = true;
        return "meta";
      }
      ;
      if (stream.match(/^\s*(\&|\*)[a-z0-9\._-]+\b/i)) {
        return "variable";
      }
      if (state.inlinePairs == 0 && stream.match(/^\s*-?[0-9\.\,]+\s?$/)) {
        return "number";
      }
      if (state.inlinePairs > 0 && stream.match(/^\s*-?[0-9\.\,]+\s?(?=(,|}))/)) {
        return "number";
      }
      if (stream.match(keywordRegex)) {
        return "keyword";
      }
    }
    if (!state.pair && stream.match(/^\s*(?:[,\[\]{}&*!|>'"%@`][^\s'":]|[^,\[\]{}#&*!|>'"%@`])[^#]*?(?=\s*:($|\s))/)) {
      state.pair = true;
      state.keyCol = stream.indentation();
      return "atom";
    }
    if (state.pair && stream.match(/^:\s*/)) {
      state.pairStart = true;
      return "meta";
    }
    state.pairStart = false;
    state.escaped = ch == "\\";
    stream.next();
    return null;
  },
  startState: function() {
    return {
      pair: false,
      pairStart: false,
      keyCol: 0,
      inlinePairs: 0,
      inlineList: 0,
      literal: false,
      escaped: false
    };
  },
  languageData: {
    commentTokens: { line: "#" }
  }
};

// node_modules/@ssddanbrown/codemirror-lang-smarty/dist/index.js
var rightDelimiter = "}";
var leftDelimiter = "{";
var baseMode = { token: (stream) => stream.skipToEnd() };
var keyFunctions = ["debug", "extends", "function", "include", "literal"];
var regs = {
  operatorChars: /[+\-*&%=<>!?]/,
  validIdentifier: /[a-zA-Z0-9_]/,
  stringChar: /['"]/
};
var last;
function cont(style, lastType) {
  last = lastType;
  return style;
}
function chain2(stream, state, parser) {
  state.tokenize = parser;
  return parser(stream, state);
}
function doesNotCount(stream, pos) {
  return false;
}
function tokenTop(stream, state) {
  const string2 = stream.string;
  let nextMatch;
  for (let scan = stream.pos; ; ) {
    nextMatch = string2.indexOf(leftDelimiter, scan);
    scan = nextMatch + leftDelimiter.length;
    if (nextMatch === -1 || !doesNotCount()) break;
  }
  if (nextMatch === stream.pos) {
    stream.match(leftDelimiter);
    if (stream.eat("*")) {
      return chain2(stream, state, tokenBlock("comment", "*" + rightDelimiter));
    } else {
      state.depth++;
      state.tokenize = tokenSmarty;
      last = "startTag";
      return "tag";
    }
  }
  if (nextMatch > -1) stream.string = string2.slice(0, nextMatch);
  const token = baseMode.token(stream, state.base);
  if (nextMatch > -1) stream.string = string2;
  return token;
}
function tokenSmarty(stream, state) {
  if (stream.match(rightDelimiter, true)) {
    {
      state.tokenize = tokenTop;
    }
    return cont("tag", null);
  }
  if (stream.match(leftDelimiter, true)) {
    state.depth++;
    return cont("tag", "startTag");
  }
  const ch = stream.next();
  if (ch === "$") {
    stream.eatWhile(regs.validIdentifier);
    return cont("variable-2", "variable");
  } else if (ch === "|") {
    return cont("operator", "pipe");
  } else if (ch === ".") {
    return cont("operator", "property");
  } else if (regs.stringChar.test(ch)) {
    state.tokenize = tokenAttribute(ch);
    return cont("string", "string");
  } else if (regs.operatorChars.test(ch)) {
    stream.eatWhile(regs.operatorChars);
    return cont("operator", "operator");
  } else if (ch === "[" || ch === "]") {
    return cont("bracket", "bracket");
  } else if (ch === "(" || ch === ")") {
    return cont("bracket", "operator");
  } else if (/\d/.test(ch)) {
    stream.eatWhile(/\d/);
    return cont("number", "number");
  } else {
    if (state.last === "variable") {
      if (ch === "@") {
        stream.eatWhile(regs.validIdentifier);
        return cont("property", "property");
      } else if (ch === "|") {
        stream.eatWhile(regs.validIdentifier);
        return cont("qualifier", "modifier");
      }
    } else if (state.last === "pipe") {
      stream.eatWhile(regs.validIdentifier);
      return cont("qualifier", "modifier");
    } else if (state.last === "whitespace") {
      stream.eatWhile(regs.validIdentifier);
      return cont("attribute", "modifier");
    }
    if (state.last === "property") {
      stream.eatWhile(regs.validIdentifier);
      return cont("property", null);
    } else if (/\s/.test(ch)) {
      last = "whitespace";
      return null;
    }
    let str = "";
    if (ch !== "/") {
      str += ch;
    }
    let c2 = null;
    while (c2 = stream.eat(regs.validIdentifier)) {
      str += c2;
    }
    for (let i = 0, j = keyFunctions.length; i < j; i++) {
      if (keyFunctions[i] === str) {
        return cont("keyword", "keyword");
      }
    }
    if (/\s/.test(ch)) {
      return null;
    }
    return cont("tag", "tag");
  }
}
function tokenAttribute(quote) {
  return function(stream, state) {
    let prevChar = null;
    let currChar = null;
    while (!stream.eol()) {
      currChar = stream.peek();
      if (stream.next() === quote && prevChar !== "\\") {
        state.tokenize = tokenSmarty;
        break;
      }
      prevChar = currChar;
    }
    return "string";
  };
}
function tokenBlock(style, terminator) {
  return function(stream, state) {
    while (!stream.eol()) {
      if (stream.match(terminator)) {
        state.tokenize = tokenTop;
        break;
      }
      stream.next();
    }
    return style;
  };
}
function cmCopyState(mode, state) {
  if (state === true) return state;
  if (mode.copyState) return mode.copyState(state);
  let nstate = {};
  for (let n in state) {
    let val = state[n];
    if (val instanceof Array) val = val.concat([]);
    nstate[n] = val;
  }
  return nstate;
}
var smarty = {
  startState: function() {
    return {
      base: true,
      tokenize: tokenTop,
      last: null,
      depth: 0
    };
  },
  copyState: function(state) {
    return {
      base: cmCopyState(baseMode, state.base),
      tokenize: state.tokenize,
      last: state.last,
      depth: state.depth
    };
  },
  innerMode: function(state) {
    if (state.tokenize === tokenTop)
      return { mode: baseMode, state: state.base };
  },
  token: function(stream, state) {
    const style = state.tokenize(stream, state);
    state.last = last;
    return style;
  },
  indent: function(state, text, line) {
    return null;
  },
  languageData: {
    commentTokens: {
      block: {
        open: leftDelimiter + "*",
        close: "*" + rightDelimiter
      }
    },
    autocomplete: keyFunctions
  }
};
export {
  c,
  clojure,
  cpp,
  csharp,
  dart,
  diff,
  fSharp,
  fortran,
  go,
  haskell,
  java,
  julia,
  kotlin,
  lua,
  msSQL,
  mySQL,
  nginx,
  oCaml,
  octave,
  pascal,
  perl,
  pgSQL,
  plSQL,
  powerShell,
  properties,
  python,
  r,
  ruby,
  rust,
  sas,
  scala,
  scheme,
  shell,
  smarty,
  sml,
  sqlite,
  standardSQL,
  stex,
  swift,
  toml,
  vb,
  vbScript,
  yaml
};
//# sourceMappingURL=legacy-modes.js.map
