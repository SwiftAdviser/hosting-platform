import React, { useEffect, useRef, useState } from 'react';
import { useGSAP } from '@gsap/react';
import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { Bot, Cpu, Diamond, ArrowUpRight, TerminalSquare, ShieldCheck, GitBranch } from 'lucide-react';

gsap.registerPlugin(ScrollTrigger);

const MagneticButton = ({ children, className }) => {
  const btnRef = useRef(null);
  const textRef = useRef(null);

  useEffect(() => {
    if (window.matchMedia('(hover: none)').matches) return;

    const btn = btnRef.current;
    if (!btn) return;
    
    const xTo = gsap.quickTo(btn, 'x', { duration: 1, ease: 'elastic.out(1, 0.3)' });
    const yTo = gsap.quickTo(btn, 'y', { duration: 1, ease: 'elastic.out(1, 0.3)' });
    const xToText = gsap.quickTo(textRef.current, 'x', { duration: 1, ease: 'elastic.out(1, 0.3)' });
    const yToText = gsap.quickTo(textRef.current, 'y', { duration: 1, ease: 'elastic.out(1, 0.3)' });

    const handleMouseMove = (e) => {
      const { clientX, clientY } = e;
      const { top, left, width, height } = btn.getBoundingClientRect();
      const x = clientX - (left + width / 2);
      const y = clientY - (top + height / 2);
      xTo(x * 0.3);
      yTo(y * 0.3);
      xToText(x * 0.15);
      yToText(y * 0.15);
    };

    const handleMouseLeave = () => {
      xTo(0);
      yTo(0);
      xToText(0);
      yToText(0);
    };

    btn.addEventListener('mousemove', handleMouseMove);
    btn.addEventListener('mouseleave', handleMouseLeave);
    return () => {
      btn.removeEventListener('mousemove', handleMouseMove);
      btn.removeEventListener('mouseleave', handleMouseLeave);
    };
  }, []);

  return (
    <button ref={btnRef} className={className}>
      <span ref={textRef} className="flex relative z-10 w-full h-full items-center justify-center gap-2">
        {children}
      </span>
    </button>
  );
};

const CustomCursor = () => {
  const cursorRef = useRef(null);

  useEffect(() => {
    if (window.matchMedia('(hover: none)').matches) return;

    const cursor = cursorRef.current;
    if (!cursor) return;

    let mouseX = 0;
    let mouseY = 0;
    let cursorX = 0;
    let cursorY = 0;

    const onMouseMove = (e) => {
      mouseX = e.clientX;
      mouseY = e.clientY;
    };

    const handleMouseOver = (e) => {
      if (e.target.closest('button, a, .interactive, .cursor-pointer')) {
        cursor.classList.add('hovering');
      } else {
        cursor.classList.remove('hovering');
      }
    };

    window.addEventListener('mousemove', onMouseMove);
    document.addEventListener('mouseover', handleMouseOver);

    const loop = () => {
      cursorX += (mouseX - cursorX) * 0.2;
      cursorY += (mouseY - cursorY) * 0.2;
      gsap.set(cursor, { x: cursorX - 4, y: cursorY - 4 });
      requestAnimationFrame(loop);
    };
    loop();

    return () => {
      window.removeEventListener('mousemove', onMouseMove);
      document.removeEventListener('mouseover', handleMouseOver);
    };
  }, []);

  return <div ref={cursorRef} className="custom-cursor hidden md:block" />;
};

const CornerNavigation = () => {
  const [scrolled, setScrolled] = useState(0);

  useEffect(() => {
    const onScroll = () => {
      const scrollPx = document.documentElement.scrollTop;
      const winHeight = document.documentElement.scrollHeight - document.documentElement.clientHeight;
      const progress = scrollPx / winHeight;
      setScrolled(Math.round(progress * 100) || 0);
    };
    window.addEventListener('scroll', onScroll);
    onScroll();
    return () => window.removeEventListener('scroll', onScroll);
  }, []);

  return (
    <div className="fixed inset-0 pointer-events-none z-[100] p-4 md:p-6 flex flex-col justify-between mix-blend-difference top-nav transition-all duration-300">
      <div className="flex justify-between items-start w-full">
        <div className="font-heading font-semibold text-xl md:text-2xl tracking-tight interactive pointer-events-auto hover:text-cyanGlow transition-colors duration-300 cursor-pointer drop-shadow-lg">
          AgentPad<span className="text-cyanGlow">.</span>
        </div>
        <MagneticButton className="interactive pointer-events-auto px-4 py-2 md:px-6 md:py-2 rounded-full border border-chrome/20 bg-abyss/20 backdrop-blur-md transition-colors duration-300 hover:border-cyanGlow hover:bg-cyanGlow hover:text-abyss font-sans text-[10px] md:text-xs font-bold uppercase tracking-widest group">
          <span className="hidden sm:inline">Init Agent</span>
          <span className="inline sm:hidden">Init</span>
          <ArrowUpRight className="w-3 h-3 md:w-4 md:h-4 transition-transform group-hover:translate-x-1 group-hover:-translate-y-1 inline-flex" />
        </MagneticButton>
      </div>

      <div className="flex justify-between items-end w-full">
        <div className="font-heading text-base md:text-lg opacity-60 drop-shadow-md">
          {scrolled < 10 ? `0${scrolled}` : scrolled > 100 ? '100' : scrolled}<span className="text-[10px] md:text-xs ml-1">%</span>
        </div>
        <div className="font-sans text-[10px] md:text-xs tracking-widest uppercase opacity-60 flex gap-4 md:gap-6 pointer-events-auto drop-shadow-md">
          <a href="https://web3.okx.com/onchainos" target="_blank" rel="noreferrer" className="interactive hover:text-cyanGlow transition-colors">OnchainOS</a>
          <a href="https://web3.okx.com/xlayer/build-x-hackathon" target="_blank" rel="noreferrer" className="interactive hover:text-cyanGlow transition-colors">X Layer</a>
        </div>
      </div>
    </div>
  );
};

const KineticHero = () => {
  const containerRef = useRef(null);
  const textRef = useRef(null);
  const bgRef = useRef(null);

  useGSAP(() => {
    gsap.from('.hero-char', {
      yPercent: 120,
      rotationX: -90,
      opacity: 0,
      stagger: 0.05,
      ease: 'back.out(1.5)',
      duration: 1.2,
      delay: 0.2
    });

    gsap.from('.hero-bg', {
      scale: 1.2,
      opacity: 0,
      duration: 2,
      ease: 'power3.out'
    });

    if (window.matchMedia('(hover: none)').matches) return; 

    const onMouseMove = (e) => {
      const { clientWidth, clientHeight } = document.documentElement;
      const xPos = (e.clientX / clientWidth - 0.5) * 2;
      const yPos = (e.clientY / clientHeight - 0.5) * 2;

      gsap.to(textRef.current, {
        rotationY: xPos * 10,
        rotationX: -yPos * 10,
        x: -xPos * 10,
        y: -yPos * 10,
        transformPerspective: 1000,
        ease: 'power3.out',
        duration: 1
      });

      gsap.to(bgRef.current, {
        x: -xPos * 30,
        y: -yPos * 30,
        ease: 'power2.out',
        duration: 2
      });
    };

    window.addEventListener('mousemove', onMouseMove);
    return () => window.removeEventListener('mousemove', onMouseMove);
  }, { scope: containerRef });

  return (
    <section ref={containerRef} className="relative min-h-screen w-full flex items-center justify-center overflow-hidden bg-abyss pointer-events-auto pt-16 md:pt-0">
      <div className="absolute inset-0 flex justify-center items-center opacity-50 z-0 hero-bg">
        <div className="w-[100vw] h-[100vw] sm:w-[80vw] sm:h-[80vw] md:w-[60vw] md:h-[60vw] bg-deepBlue rounded-[50%] md:rounded-t-[50%] overflow-hidden relative opacity-70 scale-90 md:scale-100 mt-12 md:mt-0">
          <div ref={bgRef} className="absolute -inset-10 bg-cover bg-center" style={{ backgroundImage: 'url("/hero.png")' }}>
            <div className="absolute inset-0 bg-gradient-to-t from-abyss via-abyss/40 to-cyanGlow/10 mix-blend-multiply" />
            <div className="absolute inset-0 bg-cyanGlow/20 mix-blend-color" />
          </div>
        </div>
      </div>

      <div ref={textRef} className="z-10 text-center flex flex-col items-center justify-center px-4 w-full pointer-events-none mt-8 md:mt-0">
        <h2 className="font-sans font-medium tracking-[0.3em] text-cyanGlow uppercase text-[10px] md:text-sm mb-4 md:mb-6">X Layer // Onchain OS</h2>
        <h1 className="font-heading font-bold text-[16vw] md:text-[11vw] leading-[0.9] text-chrome whitespace-nowrap flex flex-col justify-center items-center" style={{ perspective: '1000px' }}>
          <span className="block opacity-60 font-medium italic py-2">
            {'AGENTIC'.split('').map((char, i) => <span key={i} className="hero-char inline-block origin-bottom" style={{ paddingRight: i === 'AGENTIC'.length - 1 ? '0.2em' : '0' }}>{char}</span>)}
          </span>
          <span className="block py-2">
            {'LAUNCHPAD'.split('').map((char, i) => <span key={i} className="hero-char inline-block origin-bottom">{char}</span>)}
          </span>
        </h1>
        <p className="font-sans text-chrome/60 max-w-[280px] sm:max-w-md mt-6 md:mt-8 text-xs sm:text-sm md:text-base leading-relaxed">
          The end of the manual launch. An autonomous protocol powered by OnchainOS, natively executing on X Layer.
        </p>
      </div>
    </section>
  );
};

const RevealMask = () => {
  const containerRef = useRef(null);
  const maskRef = useRef(null);
  const triggerRef = useRef(null);

  useGSAP(() => {
    gsap.to(maskRef.current, {
      clipPath: 'circle(150% at 50% 50%)',
      ease: 'none',
      scrollTrigger: {
        trigger: triggerRef.current,
        start: 'top top',
        end: 'bottom bottom',
        scrub: 1,
      }
    });
  }, { scope: containerRef });

  return (
    <section ref={containerRef} className="relative w-full h-[150vh] overflow-visible z-20">
      <div ref={triggerRef} className="absolute inset-0 pointer-events-none" />
      
      <div className="sticky top-0 h-screen w-full overflow-hidden">
        <div className="absolute inset-0 flex items-center justify-center bg-chrome text-abyss p-4 sm:p-8">
          <h2 className="font-heading text-4xl sm:text-5xl md:text-6xl font-bold tracking-tight text-center max-w-4xl opacity-30 leading-snug">
            WE BELIEVE MANUAL CLICKING IS DEAD.
          </h2>
        </div>

        <div ref={maskRef} className="absolute inset-0 flex items-center justify-center bg-deepBlue text-chrome p-4 sm:p-8" style={{ clipPath: 'circle(0% at 50% 50%)' }}>
          <div className="absolute inset-0 bg-[url('/os.png')] bg-cover bg-center opacity-10 mix-blend-luminosity" />
          <h2 className="font-heading text-4xl sm:text-5xl md:text-6xl font-bold tracking-tight text-center max-w-4xl z-10 leading-snug">
            WE BUILD FOR THE <br/><span className="text-cyanGlow italic font-light">AGENTIC FUTURE.</span>
          </h2>
        </div>
      </div>
    </section>
  );
};

const AccordionItem = ({ index, title, desc, features, expanded, setExpanded, src }) => {
  const contentRef = useRef(null);

  useEffect(() => {
    if (!contentRef.current) return;
    if (expanded) {
      gsap.to(contentRef.current, { height: 'auto', opacity: 1, duration: 0.6, ease: 'power3.inOut' });
    } else {
      gsap.to(contentRef.current, { height: '0px', opacity: 0, duration: 0.6, ease: 'power3.inOut' });
    }
  }, [expanded]);

  return (
    <div className="border-b border-chrome/10 w-full overflow-hidden">
      <div 
        className="flex items-center justify-between py-6 md:py-8 px-2 md:px-4 cursor-pointer interactive group"
        onClick={() => setExpanded(expanded ? null : index)}
      >
        <div className="flex flex-col md:flex-row md:items-baseline gap-1 md:gap-8 max-w-[85%]">
          <span className="font-sans text-cyanGlow text-[10px] md:text-sm font-bold tracking-widest">0{index + 1}</span>
          <h3 className="font-heading text-2xl sm:text-3xl md:text-5xl font-semibold opacity-80 group-hover:opacity-100 transition-opacity uppercase tracking-tight leading-none pt-1">
            {title}
          </h3>
        </div>
        <div className={`shrink-0 relative w-6 h-6 md:w-8 md:h-8 rounded-full border border-chrome/20 flex items-center justify-center transition-transform duration-500 hover:border-cyanGlow ${expanded ? 'rotate-90 bg-cyanGlow text-abyss border-transparent' : ''}`}>
          <ArrowUpRight className={`w-3 h-3 md:w-4 md:h-4 transition-transform duration-500 ${expanded ? 'rotate-45' : ''}`} />
        </div>
      </div>

      <div ref={contentRef} className="h-0 opacity-0 overflow-hidden" style={{ display: 'none' }}>
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-8 px-2 md:px-4 pb-8 md:pb-12 pt-4">
          <div className="flex flex-col h-full justify-between">
            <p className="font-sans text-chrome/70 text-sm sm:text-base md:text-xl leading-relaxed mb-8 md:mb-12 max-w-lg">
              {desc}
            </p>
            <div className="flex flex-col gap-3 md:gap-4 mb-8 lg:mb-0">
              {features.map((feat, i) => (
                <div key={i} className="flex items-center gap-3 border-l-2 border-cyanGlow/50 pl-3 md:pl-4">
                  <div className="shrink-0">{feat.icon}</div>
                  <span className="font-sans font-medium text-xs sm:text-sm text-chrome/90">{feat.text}</span>
                </div>
              ))}
            </div>
            <button className="interactive mt-4 lg:mt-12 w-fit px-6 py-2 md:px-8 md:py-3 rounded-full bg-chrome text-abyss font-sans font-bold text-[10px] md:text-sm uppercase tracking-wider hover:bg-cyanGlow transition-colors duration-300">
              Explore Module
            </button>
          </div>
          <div className="relative h-48 sm:h-64 lg:h-full min-h-[200px] lg:min-h-[300px] rounded-[12px] md:rounded-[16px] overflow-hidden group border border-cyanGlow/20">
            <div className="absolute inset-0 bg-cover bg-center transition-transform duration-1000 group-hover:scale-105" style={{ backgroundImage: `url(${src})` }} />
            <div className="absolute inset-0 bg-gradient-to-tr from-abyss/80 to-transparent mix-blend-multiply" />
            <div className="absolute inset-0 bg-cyanGlow/10 mix-blend-color" />
          </div>
        </div>
      </div>
    </div>
  );
};

const AccordionVault = () => {
  const [expanded, setExpanded] = useState(0);

  const pillars = [
    {
      title: "Natural Language OS",
      desc: "Speak your market into existence. Pass structural variables via natural language UI, and our infrastructure translates your intent instantly into audited smart contracts on X Layer.",
      src: "/os.png",
      features: [
        { icon: <TerminalSquare className="w-4 h-4 md:w-5 md:h-5 text-cyanGlow" />, text: "Natural Language CLI" },
        { icon: <Cpu className="w-4 h-4 md:w-5 md:h-5 text-cyanGlow" />, text: "Built-in RPC Abstraction" },
      ]
    },
    {
      title: "Clanker-Grade Execution",
      desc: "Zero human intervention. Your AI agent independently deploys the token, provisions the foundational liquidity pool, and triggers automated market-making parameters.",
      src: "/clanker.png",
      features: [
        { icon: <Bot className="w-4 h-4 md:w-5 md:h-5 text-cyanGlow" />, text: "Autonomous Deployment" },
        { icon: <GitBranch className="w-4 h-4 md:w-5 md:h-5 text-cyanGlow" />, text: "Strategy Execution Loop" },
      ]
    },
    {
      title: "Canonical Liquidity",
      desc: "Agentic creation with traditional composability. Every deployment results in a standardized market, ensuring your token plugs seamlessly into the existing X Layer DeFi ecosystem.",
      src: "/traditional.png",
      features: [
        { icon: <Diamond className="w-4 h-4 md:w-5 md:h-5 text-cyanGlow" />, text: "Canonical Token Deployment" },
        { icon: <ShieldCheck className="w-4 h-4 md:w-5 md:h-5 text-cyanGlow" />, text: "Liquidity Guardrails" },
      ]
    }
  ];

  return (
    <section className="relative w-full bg-abyss py-16 md:py-32 z-10 pt-32">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
        <h2 className="font-sans text-[10px] md:text-xs tracking-[0.2em] uppercase text-cyanGlow mb-8 md:mb-16 px-2 md:px-4">System Pillars</h2>
        <div className="flex flex-col">
          {pillars.map((pillar, i) => (
            <AccordionItem 
              key={i} 
              index={i} 
              {...pillar} 
              expanded={expanded === i} 
              setExpanded={setExpanded} 
            />
          ))}
        </div>
      </div>
    </section>
  );
};

const VaultDoor = () => {
  const containerRef = useRef(null);
  const textRef = useRef(null);

  useGSAP(() => {
    gsap.from(textRef.current, {
      y: 100,
      opacity: 0,
      duration: 1.5,
      ease: 'power4.out',
      scrollTrigger: {
        trigger: containerRef.current,
        start: 'top 80%',
      }
    });
  }, { scope: containerRef });

  return (
    <footer ref={containerRef} className="relative w-full bg-abyss pt-24 md:pt-32 pb-8 md:pb-12 flex flex-col items-center justify-center z-10 px-4 md:px-8 border-t border-chrome/10 overflow-hidden">
      <div className="absolute top-0 left-1/2 -translate-x-1/2 w-[1px] h-24 md:h-32 bg-gradient-to-b from-transparent to-cyanGlow opacity-50" />
      
      <div className="flex flex-col items-center mb-16 md:mb-24 mt-12 md:mt-16 w-full text-center px-4">
        <div className="w-3 h-3 md:w-4 md:h-4 bg-cyanGlow rounded-full animate-pulse shadow-[0_0_20px_theme('colors.cyanGlow')] mb-6 md:mb-8" />
        <h2 className="font-sans font-medium tracking-[0.2em] text-chrome/60 uppercase text-[10px] md:text-xs mb-6 md:mb-8">System Online</h2>
        
        <button className="interactive group relative px-6 py-4 md:px-12 md:py-6 rounded-full bg-chrome text-abyss overflow-hidden transition-transform hover:scale-105 duration-300 w-fit mx-auto">
          <div className="absolute inset-0 bg-cyanGlow translate-y-full group-hover:translate-y-0 transition-transform duration-500 ease-[cubic-bezier(0.25,1,0.5,1)]" />
          <span className="relative z-10 font-heading text-lg sm:text-lg md:text-2xl font-bold tracking-tight uppercase flex items-center justify-center gap-2 md:gap-4 whitespace-nowrap">
            Initialize Test Agent <ArrowUpRight className="w-5 h-5 md:w-6 md:h-6 shrink-0" />
          </span>
        </button>
        <p className="mt-6 md:mt-8 font-sans text-xs md:text-sm text-chrome/40 max-w-[280px] md:max-w-sm text-center leading-relaxed">
          Deploy a test token via OnchainOS agent natively on X Layer to verify mechanics.
        </p>
      </div>

      <div ref={textRef} className="w-full overflow-hidden flex justify-center">
        <h1 className="font-heading font-black text-[15vw] leading-[0.8] text-center tracking-tight text-chrome opacity-5 hover:opacity-20 transition-opacity duration-700 cursor-default">
          AGENTPAD
        </h1>
      </div>

      <div className="w-full flex flex-col md:flex-row justify-between items-center sm:items-end mt-12 md:mt-12 pt-6 md:pt-8 border-t border-chrome/10 gap-2 md:gap-0">
        <div className="font-sans text-[10px] md:text-xs text-chrome/40 uppercase tracking-widest text-center">
          © 2026 X Layer Hackathon
        </div>
        <div className="font-sans text-[10px] md:text-xs text-chrome/40 uppercase tracking-widest text-center">
          Version 1.0.0
        </div>
      </div>
    </footer>
  );
};

export default function App() {
  const [mounted, setMounted] = useState(false);
  useEffect(() => setMounted(true), []);

  if (!mounted) return null;

  return (
    <div className="w-full h-full relative selection:bg-cyanGlow selection:text-abyss">
      <CustomCursor />
      <CornerNavigation />
      <main className="w-full overflow-x-hidden">
        <KineticHero />
        <RevealMask />
        <AccordionVault />
        <VaultDoor />
      </main>
    </div>
  );
}
